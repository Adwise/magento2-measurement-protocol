<?php

namespace Adwise\Analytics\Service;

use Adwise\Analytics\Helper\Data as DataHelper;
use Magento\Sales\Model\Order as MagentoOrder;
use Magento\Sales\Model\Order\Creditmemo;
use Psr\Log\LoggerInterface;

class AnalyticsService
{
    /**
     *  Old statuses
     */
    const EXPORT_STATUS_OLD_EXPORTED_POSITIVE = 1;
    const EXPORT_STATUS_OLD_EXPORTED_NEGATIVE = 0;

    /**
     * This is a new order that this module has never handled
     */
    const EXPORT_STATUS_NEW_NEVER_EXPORTED = null;

    /**
     * We have exported the order in full, and only allow refunds or cancellation
     */
    const EXPORT_STATUS_PROCESSED = 2;

    /**
     * Order is cancelled. No state transition from this.
     */
    const EXPORT_STATUS_NEW_CANCELLED = 3;

    /**
     * Order is fully refunded. No state transition from this
     */
    const EXPORT_STATUS_FULLY_REFUNDED = 4;

    /**
     * Order is placed while order hits where disabled, this status is used to allow these orders to still be refunded
     */
    const EXPORT_STATUS_ORDER_HIT_DISABLED = 5;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    protected $analyticsCaller;

    const ERROR_UNKNOWN_ORDER_STATE = -1;
    const ERROR_ORDER_OLD = -2;
    const ERROR_ORDER_ALREADY_EXPORTED = -3;
    const ERROR_CANCELLATION_NOT_ALLOWED = -4;
    const ERROR_CREDITMEMO_NOT_ALLOWED = -5;
    const ERROR_CREDITMEMO_NEGATIVE_BALANCE = -6;
    const ERROR_DISABLED = -7;

    const SUCCESS = 0;

    public function __construct (
        LoggerInterface $logger,
        DataHelper $dataHelper,
        AnalyticsCaller $analyticsCaller
    ) {
        $this->logger = $logger;
        $this->dataHelper = $dataHelper;
        $this->analyticsCaller = $analyticsCaller;
    }

    public function handleOrder(MagentoOrder $order) {
        $this->dataHelper->setStoreId($order->getStoreId());

        if ($this->dataHelper->getIsEnabled() && $this->dataHelper->getAnyEnabled() && !$this->dataHelper->getAnyPurchaseEventEnabled()) {
            // if the order hit is not enabled, still set the aw_exported_amount to order value to allow refunds
            $order->setAwAnalyticsAmount($order->getGrandTotal());
            $order->setAwAnalyticsExport(self::EXPORT_STATUS_ORDER_HIT_DISABLED);
            $order->save();
            return self::ERROR_DISABLED;
        }

        if ((!$this->dataHelper->getIsEnabled()) ||
            (!$this->dataHelper->getAnyEnabled()) ||
            (!($this->dataHelper->getAnyPurchaseEventEnabled()))
        ) {
            return self::ERROR_DISABLED;
        }

        if($order->getState() === MagentoOrder::STATE_PROCESSING) {
            return $this->handleProcessingOrder($order);
        } else if($order->getState() === MagentoOrder::STATE_CANCELED){
            return $this->handleCancelledOrder($order);
        } else {
            $this->logger->debug('[AnalyticsService] Not handling order since state ' . $order->getState() . ' is not mapped');
            return self::ERROR_UNKNOWN_ORDER_STATE;
        }
    }

    private function handleProcessingOrder(MagentoOrder $order): int {
        if ($this->isOldOrder($order)) {
            $this->logger->warning('[AnalyticsService] Not handling processing state on order since it has been previously exported', [$order->getIncrementId()]);
            return self::ERROR_ORDER_OLD;
        }

        $awExportState = $this->intval($order->getAwAnalyticsExport());

        if($awExportState !== self::EXPORT_STATUS_NEW_NEVER_EXPORTED) {
            $this->logger->warning('[AnalyticsService] Not handling processing state on order since we have previously exported', [$order->getIncrementId()]);
            return self::ERROR_ORDER_ALREADY_EXPORTED;
        }

        // ensure this is not a undo action
        $this->analyticsCaller->handleOrder($order);

        $amountExported = $order->getGrandTotal();
        $order->setAwAnalyticsExport(self::EXPORT_STATUS_PROCESSED);
        $order->setAwAnalyticsAmount($amountExported);
        $order->addCommentToStatusHistory('[AnalyticsService]: Sent order with amount ' . $amountExported);
        $this->logger->debug('[AnalyticsService] Exported ' . $order->getIncrementId() . ' with amount ' . $amountExported);
        $order->save();
        return self::SUCCESS;
    }

    private function handleCancelledOrder(MagentoOrder $order): int
    {
        // Ensure cancellation is allowed
        if (!$this->isCancellationAllowed($order)) {
            $this->logger->warning('[AnalyticsService] Not handling cancellation on order ' . $order->getIncrementId() . ' since it is in a invalid state: ' . $order->getAwAnalyticsExport());
            return self::ERROR_CANCELLATION_NOT_ALLOWED;
        }

        try {
            $this->analyticsCaller->handleOrder($order);
        } catch (\Exception $exception){
            $this->logger->error('Failure updating order ' . $order->getIncrementId() . ' ' . $exception->getMessage(), $exception->getTrace());
            return -1337;
        }

        // Mark this order as cancelled
        $order->setAwAnalyticsExport(self::EXPORT_STATUS_NEW_CANCELLED);
        // Since it's undone we can safely assume the net total at GA is 0
        $order->setAwAnalyticsAmount(0);
        $order->addCommentToStatusHistory('[AnalyticsService]: Sent cancellation');
        $this->logger->debug('[AnalyticsService] Exported cancellation for ' . $order->getIncrementId());
        $order->save();
        return self::SUCCESS;
    }

    public function handleCreditmemo(Creditmemo $creditmemo) {
        $this->dataHelper->setStoreId($creditmemo->getStoreId());

        if ((!$this->dataHelper->getIsEnabled()) ||
            (!$this->dataHelper->getAnyEnabled()) ||
            (!$this->dataHelper->getAnyRefundEventEnabled())
        ) {
            return self::ERROR_DISABLED;
        }

        /** @var MagentoOrder $order */
        $order = $creditmemo->getOrder();

        // Make sure it has been exported before
        if(!($this->isCreditmemoAllowed($order))){
            $this->logger->warning('[AnalyticsService] Not handling CreditMemo ' . $creditmemo->getIncrementId() . ' since order has not been processed');
            return self::ERROR_CREDITMEMO_NOT_ALLOWED;
        }

        $amountRefund = round($creditmemo->getGrandTotal(), 4, PHP_ROUND_HALF_EVEN);
        $currentAmountInGa = floatval($order->getAwAnalyticsAmount());
        $afterRefund = $currentAmountInGa - $amountRefund;
        if($afterRefund < 0) {
            $this->logger->warning('[AnalyticsService] Not handling CreditMemo ' . $creditmemo->getIncrementId() . ' since balance would be ' . $afterRefund . ' after refund');
            return self::ERROR_CREDITMEMO_NEGATIVE_BALANCE;
        }

        try {
            $this->analyticsCaller->handleCreditMemo($creditmemo);
        } catch (\Exception $exception){
            $this->logger->error('Failure updating creditMemo ' . $creditmemo->getIncrementId() . ' ' . $exception->getMessage(), $exception->getTrace());
            return -1337;
        }

        if($afterRefund == 0) {
            $order->setAwAnalyticsExport(self::EXPORT_STATUS_FULLY_REFUNDED);
        }

        $order->addCommentToStatusHistory('[AnalyticsService]: Sent credit memo with ' . $amountRefund . ' remaining ' . $afterRefund);
        $order->setAwAnalyticsAmount($afterRefund);
        $order->save();
        return self::SUCCESS;
    }

    private function isCreditmemoAllowed(MagentoOrder $order)
    {
        $state = $this->intval($order->getAwAnalyticsExport());
        return ($state === self::EXPORT_STATUS_OLD_EXPORTED_POSITIVE || $state === self::EXPORT_STATUS_PROCESSED || $state === self::EXPORT_STATUS_ORDER_HIT_DISABLED);
    }

    private function isCancellationAllowed(MagentoOrder $order)
    {
        $state = $this->intval($order->getAwAnalyticsExport());
        return ($state === self::EXPORT_STATUS_OLD_EXPORTED_POSITIVE || $state === self::EXPORT_STATUS_PROCESSED);
    }

    private function isOldOrder(MagentoOrder $order)
    {
        $state = $this->intval($order->getAwAnalyticsExport());
        return $state === self::EXPORT_STATUS_OLD_EXPORTED_NEGATIVE || $state === self::EXPORT_STATUS_OLD_EXPORTED_POSITIVE;
    }

    private function intval($val) {
        if($val === null){
            return null;
        }
        return intval($val);
    }
}
