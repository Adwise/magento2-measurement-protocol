<?php

namespace Adwise\Analytics\Model\MP;

use Adwise\Analytics\Api\OrderDataProviderInterface;
use Magento\Sales\Api\Data\OrderInterface;

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
                $data = array_replace_recursive($data, $result);
            }
        }

        // items needs to be without keys
        if(isset($data['items'])) {
            $data['items'] = array_values($data['items']);
        }

        return $data;
    }
}
