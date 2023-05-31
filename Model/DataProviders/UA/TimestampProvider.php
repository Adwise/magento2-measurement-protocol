<?php

namespace Adwise\Analytics\Model\DataProviders\UA;

use Adwise\Analytics\Api\OrderDataProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Helper\Product;
use DateTime;
use Magento\Sales\Api\Data\OrderInterface;

class TimestampProvider implements OrderDataProviderInterface
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

    public function getData(OrderInterface $order)
    {
        if ($this->dataHelper->getUACDTimestamp()) {
            $now = new DateTime();
            $data[$this->dataHelper->getUACDTimestamp()] = $now->format('c');
        }
    }
}
