<?php

namespace Adwise\Analytics\Observer\Sales\CreditMemo;

use Adwise\Analytics\Service\AnalyticsService;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as MagentoOrder;
use Magento\Sales\Model\Order\Creditmemo;

class SaveAfter implements ObserverInterface
{

    /** @var AnalyticsService */
    protected $analyticsService;

    /**
     * SaveAfter constructor.
     * @param DataHelper $dataHelper
     * @param CreditmemoHelper $creditmemoHelper
     */
    public function __construct(
        AnalyticsService $analyticsService
    ) {
        $this->analyticsService = $analyticsService;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();

        $this->analyticsService->handleCreditmemo($creditmemo);
    }
}
