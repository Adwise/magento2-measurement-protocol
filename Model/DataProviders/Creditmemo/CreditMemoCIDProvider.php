<?php

namespace Adwise\Analytics\Model\DataProviders\Creditmemo;

use Adwise\Analytics\Api\CreditMemoProviderInterface;
use Adwise\Analytics\Model\DataProviders\CIDProvider;
use Magento\Sales\Api\Data\CreditmemoInterface;


class CreditMemoCIDProvider implements CreditMemoProviderInterface
{
    /**
     * @var CIDProvider $cidProvider
     */
    private $CIDProvider;

    /**
     * CreditMemoCIDProvider constructor.
     * @param CIDProvider $CIDProvider
     */
    public function __construct(CIDProvider $CIDProvider)
    {
        $this->CIDProvider = $CIDProvider;
    }

    public function getData(CreditmemoInterface $creditmemo) {
        return $this->CIDProvider->getData($creditmemo->getOrder());
    }
}
