<?php

namespace Adwise\Analytics\Api;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * @deprecated
 * @see \Adwise\Analytics\Model\MP\OrderDataProviders
 */
interface OrderDataProviderInterface
{
    public function getData(OrderInterface $order);
}
