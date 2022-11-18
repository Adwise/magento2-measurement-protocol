<?php

namespace Adwise\Analytics\Model\DataProviders\MP\Creditmemo;

use Adwise\Analytics\Api\CreditMemoProviderInterface;
use Adwise\Analytics\Model\DataProviders\MP\CIDProvider;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Adwise\Analytics\Helper\Data;

class CreditMemoCIDProvider implements CreditMemoProviderInterface
{
    /**
     * @var CIDProvider $cidProvider
     */
    private $CIDProvider;
    private Data $dataHelper;

    /**
     * CreditMemoCIDProvider constructor.
     * @param CIDProvider $CIDProvider
     */
    public function __construct(CIDProvider $CIDProvider, Data $dataHelper)
    {
        $this->CIDProvider = $CIDProvider;
        $this->dataHelper = $dataHelper;
    }

    public function getData(CreditmemoInterface $creditmemo) {
        $data = [
            'cid' => $this->CIDProvider->getClientId($creditmemo->getOrder()),
            $this->dataHelper->getUACDCIDKnown() => $creditmemo->getOrder()->getGaClientId() ? 'Known' : 'Unknown'
        ];

        return $data;
    }
}
