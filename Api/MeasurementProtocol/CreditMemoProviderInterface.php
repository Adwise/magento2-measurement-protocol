<?php

namespace Adwise\Analytics\Api\MeasurementProtocol;

use Magento\Sales\Api\Data\CreditmemoInterface;

interface CreditMemoProviderInterface
{
    public function getData(CreditmemoInterface $creditmemo);
}
