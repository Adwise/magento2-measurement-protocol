<?php

namespace Adwise\Analytics\Model\DataProviders\Creditmemo;

use Adwise\Analytics\Api\CreditMemoProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Helper\Product;
use Magento\Sales\Api\Data\CreditmemoInterface;

class BaseCreditMemoProvider implements CreditMemoProviderInterface
{

    /**
     * @var Data
     */
    private $dataHelper;

    public function __construct(
        Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    public function getData(CreditmemoInterface $creditmemo)
    {

        $data = [
            'currency' => $creditmemo->getOrderCurrencyCode(),
            'transaction_id' => $creditmemo->getOrder()->getIncrementId(),
            'value' => $this->round($creditmemo->getGrandTotal()),
        ];
        $order = $creditmemo->getOrder();

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

        return $data;
    }

    private function round($number){
        return round($number, 4, PHP_ROUND_HALF_EVEN);
    }
}
