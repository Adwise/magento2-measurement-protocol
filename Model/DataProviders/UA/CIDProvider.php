<?php

namespace Adwise\Analytics\Model\DataProviders\UA;

use Adwise\Analytics\Api\OrderDataProviderInterface;
use Adwise\Analytics\Helper\Data;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Sales\Api\Data\OrderInterface;

class CIDProvider implements OrderDataProviderInterface
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

    public function getData(OrderInterface $order)
    {
        $data = [];
        if ($order->getGaClientId()) {
            $data['cid'] = $order->getGaClientId();
            if ($this->dataHelper->getUACDCIDKnown()) {
                $data[$this->dataHelper->getUACDCIDKnown()] = 'Known';
            }
        } else {
            $data['cid'] = $this->identityService->generateId();
            if ($this->dataHelper->getUACDCIDKnown()) {
                $data[$this->dataHelper->getUACDCIDKnown()] = 'Unknown';
            }
        }

        return $data;
    }
}
