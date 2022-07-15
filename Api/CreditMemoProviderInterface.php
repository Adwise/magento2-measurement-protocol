<?php

namespace Adwise\Analytics\Api;

use Magento\Sales\Api\Data\CreditmemoInterface;

interface CreditMemoProviderInterface
{
    public function getData(CreditmemoInterface $creditmemo);
}
