<?php

namespace Adwise\Analytics\Model\DataProviders;

use Adwise\Analytics\Api\MeasurementProtocol\ClientIdProviderInterface;
use Adwise\Analytics\Helper\Data;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Sales\Api\Data\OrderInterface;

class CIDProvider implements ClientIdProviderInterface
{
    /**
     * @var IdentityGeneratorInterface
     */
    protected $identityService;

    protected $dataHelper;

    public function __construct (
        IdentityGeneratorInterface $identityService,
        Data $data
    ) {
        $this->identityService = $identityService;
        $this->dataHelper = $data;
    }

    public function getClientId(OrderInterface $order)
    {
        if ($order->getGaClientId()) {
            return $order->getGaClientId();
        }

        // generate random client id
        return $this->identityService->generateId();
    }
}
