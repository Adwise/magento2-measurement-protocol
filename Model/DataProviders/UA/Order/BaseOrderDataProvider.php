<?php

namespace Adwise\Analytics\Model\DataProviders\UA\Order;

use Adwise\Analytics\Api\OrderDataProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Helper\Product;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as MagentoOrder;

class BaseOrderDataProvider implements OrderDataProviderInterface
{

    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var Product
     */
    private $productHelper;

    public function __construct(
        Data $dataHelper,
        Product $productHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->productHelper = $productHelper;
    }

    function getData(OrderInterface $order)
    {
        $priceMod = 1;

        if($order->getStatus() === MagentoOrder::STATE_CANCELED){
            $priceMod = -1;
        }

        $data = [
            'v' => 1,
            't' => $this->dataHelper->getHitType(),
            'el' => $order->getIncrementId(),
            'tid' => $this->dataHelper->getUATrackingId(),
            'ti' => $order->getIncrementId(),
            'ta' => $this->dataHelper->getUATransactionAffiliation(),
            'tr' => $priceMod * $this->round($order->getGrandTotal()),
            'tt' => $priceMod * $this->round($order->getTaxAmount()),
            'ts' => $priceMod * $this->round($order->getShippingAmount()),
            'pa' => 'purchase',
            'ec' => $this->dataHelper->getUAEventCategory(),
            'ea' => $this->dataHelper->getUaPurchaseEventAction(),
        ];

        return $data;
    }

    private function round($number){
        return round($number, 4, PHP_ROUND_HALF_EVEN);
    }
}
