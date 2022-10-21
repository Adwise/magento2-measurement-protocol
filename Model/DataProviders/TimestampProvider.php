<?php

namespace Adwise\Analytics\Model\DataProviders;

use Adwise\Analytics\Api\OrderDataProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Helper\Product;
use DateTime;
use Magento\Sales\Api\Data\OrderInterface;

class TimestampProvider
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

    public function parseCreatedAtForMP($createdAt)
    {
        // parse the date to a timestamp
        $date = new DateTime($createdAt);
        // if its longer than 3 days ago return null, else return $date as microseconds
        return $date->getTimestamp() > (time() - 259200) ? $date->getTimestamp() * 1000 : null;
    }
}
