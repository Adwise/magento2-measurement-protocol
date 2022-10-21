<?php

namespace Adwise\Analytics\Model\MP;

use Adwise\Analytics\Api\CreditMemoProviderInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;

class CreditMemoDataProviders implements CreditMemoProviderInterface
{

    /**
     * @var CreditMemoProviderInterface[]
     */
    private $creditMemoDataProviders;

    public function __construct (
        array $providers
    ) {
        $this->creditMemoDataProviders = $providers;
    }

    public function getData(CreditmemoInterface $creditmemo)
    {
        $data = [];
        foreach($this->creditMemoDataProviders as $creditMemoDataProvider){
            $result = $creditMemoDataProvider->getData($creditmemo);
            if(is_array($result)) {
                $data = array_replace_recursive($data, $result);
            }
        }

        // items needs to be without keys
        if(isset($data['items'])) {
            $data['items'] = array_values($data['items']);
        }

        return ['name' => 'refund', 'params' => $data];
    }
}
