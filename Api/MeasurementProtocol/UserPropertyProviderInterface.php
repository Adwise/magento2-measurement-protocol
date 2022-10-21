<?php

namespace Adwise\Analytics\Api\MeasurementProtocol;

use Magento\Sales\Api\Data\OrderInterface;

interface UserPropertyProviderInterface
{
    public function getData(OrderInterface $order);
}
