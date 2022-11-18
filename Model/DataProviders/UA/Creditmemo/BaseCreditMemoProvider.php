<?php

namespace Adwise\Analytics\Model\DataProviders\UA\Creditmemo;

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
            'v' => 1,
            'tid' => $this->dataHelper->getUATrackingId(),
            't' => $this->dataHelper->getHitType(),
            'el' => $creditmemo->getOrder()->getIncrementId(),
            'ti' =>  $creditmemo->getOrder()->getIncrementId(),
            'pa' => $this->dataHelper->getCreditMemoProductAction(),
            'ec' => $this->dataHelper->getUAEventCategory(),
            'ea' => $this->dataHelper->getUARefundEventAction(),
            'ni' => '1', // This is a no-interaction event
        ];

        return $data;
    }

    private function round($number){
        return round($number, 4, PHP_ROUND_HALF_EVEN);
    }
}
