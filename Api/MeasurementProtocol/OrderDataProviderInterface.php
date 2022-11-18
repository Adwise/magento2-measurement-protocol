<?php

namespace Adwise\Analytics\Api\MeasurementProtocol;

use Magento\Sales\Api\Data\OrderInterface;

interface OrderDataProviderInterface
{
    public function getData(OrderInterface $order);
}
