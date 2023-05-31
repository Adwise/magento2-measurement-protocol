<?php

namespace Adwise\Analytics\Model\DataProviders\MP;

use Adwise\Analytics\Api\MeasurementProtocol\UserIdProviderInterface;
use Magento\Sales\Api\Data\OrderInterface;

class UserIdProvider implements UserIdProviderInterface
{
    public function getUserId(OrderInterface $order) {
        return md5($order->getCustomerEmail());
    }
}
