<?php

namespace Adwise\Analytics\Model;

use Adwise\Analytics\Api\OrderDataProviderInterface;
use Magento\Sales\Api\Data\OrderInterface;
/**
 * @deprecated
 * @see \Adwise\Analytics\Model\MP\OrderDataProviders
 */
class OrderDataProviders implements OrderDataProviderInterface
{
    /**
     * @var OrderDataProviderInterface[]
     */
    private $orderDataProviders;

    public function __construct (
        array $providers
    ) {
        $this->orderDataProviders = $providers;
    }

    public function getData(OrderInterface $order)
    {
        $data = [];
        foreach($this->orderDataProviders as $orderDataProvider){
            $result = $orderDataProvider->getData($order);
            if(is_array($result)) {
                $data = array_merge($data, $result);
            }
        }
        return $data;
    }
}
