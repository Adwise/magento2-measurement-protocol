<?php

namespace Adwise\Analytics\Model\DataProviders\Order;

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
            'tid' => $this->dataHelper->getTrackingId(),
            'ti' => $order->getIncrementId(),
            'ta' => $this->dataHelper->getTransactionAffiliation(),
            'tr' => $priceMod * $this->round($order->getGrandTotal()),
            'tt' => $priceMod * $this->round($order->getTaxAmount()),
            'ts' => $priceMod * $this->round($order->getShippingAmount()),
            'pa' => 'purchase'
        ];

        switch ($this->dataHelper->getHitType()) {
            case 'event':
                $data['ec'] = $this->dataHelper->getEventCategory();
                $data['ea'] = $this->dataHelper->getEventAction();
                break;
            case 'pageview':
                $data['dh'] = $this->dataHelper->getPageviewHostname();
                $data['dp'] = $this->dataHelper->getPageviewPath();
                $data['dt'] = $this->dataHelper->getPageviewTitle();
                break;
        }
        return $data;
    }

    private function round($number){
        return round($number, 4, PHP_ROUND_HALF_EVEN);
    }
}
