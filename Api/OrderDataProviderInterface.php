<?php

namespace Adwise\Analytics\Api;

use Magento\Sales\Api\Data\OrderInterface;

interface OrderDataProviderInterface
{
    public function getData(OrderInterface $order);
}
