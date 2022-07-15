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
            'v' => 1,
            'tid' => $this->dataHelper->getTrackingId(),
            't' => $this->dataHelper->getHitType(),
            'el' => $creditmemo->getOrder()->getIncrementId(),
            'ti' =>  $creditmemo->getOrder()->getIncrementId(),
            'pa' => $this->dataHelper->getCreditMemoProductAction(),
            'ni' => '1', // This is a no-interaction event
        ];

        switch ($this->dataHelper->getHitType()) {
            case 'event':
                $data['ec'] = $this->dataHelper->getEventCategory();
                $data['ea'] = $this->dataHelper->getCreditMemoEventAction();
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
