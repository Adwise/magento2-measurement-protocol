<?php

namespace Adwise\Analytics\Api\MeasurementProtocol;

use Magento\Sales\Api\Data\OrderInterface;

interface ClientIdProviderInterface
{
    public function getClientId(OrderInterface $order);
}
