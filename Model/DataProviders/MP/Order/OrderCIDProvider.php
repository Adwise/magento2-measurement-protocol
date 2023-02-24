<?php

namespace Adwise\Analytics\Model\DataProviders\MP\Order;

use Adwise\Analytics\Api\MeasurementProtocol\OrderDataProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Helper\Product;
use Adwise\Analytics\Model\DataProviders\MP\CIDProvider;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as MagentoOrder;

class OrderCIDProvider implements OrderDataProviderInterface
{

    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var CIDProvider $cidProvider
     */
    private $CIDProvider;

    public function __construct(
        Data $dataHelper,
        CIDProvider $productHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->productHelper = $productHelper;
    }

    function getData(OrderInterface $order)
    {
        return [
            'cid' => $this->CIDProvider->getClientId($order),
            $this->dataHelper->getUACDCIDKnown() => $order->getGaClientId() ? 'Known' : 'Unknown',
            'session_id' => $order->getGaSessionId(),
        ];
    }
}
