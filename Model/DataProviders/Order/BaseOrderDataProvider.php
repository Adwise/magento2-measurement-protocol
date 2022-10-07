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
            'currency' => $order->getOrderCurrencyCode(),
            'transaction_id' => $order->getIncrementId(),
            'value' => $priceMod * $this->round($order->getGrandTotal()),
        ];

        if ($order->getCouponCode()) {
            $data['coupon'] = $order->getCouponCode();
        }

        if ($order->getShippingAmount()) {
            $data['shipping'] = $priceMod * $this->round($order->getShippingAmount());
        }

        if ($order->getTaxAmount()) {
            $data['tax'] = $priceMod * $this->round($order->getTaxAmount());
        }

        return $data;
    }

    private function round($number){
        return round($number, 4, PHP_ROUND_HALF_EVEN);
    }
}
