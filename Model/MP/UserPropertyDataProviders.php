<?php

namespace Adwise\Analytics\Model\MP;

use Adwise\Analytics\Api\MeasurementProtocol\UserPropertyProviderInterface;
use Magento\Sales\Api\Data\OrderInterface;

class UserPropertyDataProviders implements UserPropertyProviderInterface
{
    /**
     * @var UserPropertyProviderInterface[]
     */
    private $userPropertyProviderInterfaces;

    public function __construct (
        array $providers
    ) {
        $this->userPropertyProviderInterfaces = $providers;
    }

    public function getData(OrderInterface $order)
    {
        $data = [];
        foreach($this->userPropertyProviderInterfaces as $userPropertyProviderInterface){
            $result = $userPropertyProviderInterface->getData($order);
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
