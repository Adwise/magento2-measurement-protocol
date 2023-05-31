<?php

namespace Adwise\Analytics\Api\MeasurementProtocol;

use Magento\Sales\Api\Data\OrderInterface;

interface UserIdProviderInterface
{
    public function getUserId(OrderInterface $order);
}
