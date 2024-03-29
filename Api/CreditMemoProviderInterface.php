<?php

namespace Adwise\Analytics\Api;

use Magento\Sales\Api\Data\CreditmemoInterface;

/**
 * @deprecated
 * @see \Adwise\Analytics\Model\MP\CreditMemoDataProviders
 */
interface CreditMemoProviderInterface
{
    public function getData(CreditmemoInterface $creditmemo);
}
