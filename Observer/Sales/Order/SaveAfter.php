<?php

namespace Adwise\Analytics\Observer\Sales\Order;

use Adwise\Analytics\Service\AnalyticsService;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order as MagentoOrder;

class SaveAfter implements ObserverInterface
{

    /** @var AnalyticsService */
    protected $analyticsService;

    /**
     * SaveAfter constructor.
     */
    public function __construct(
        AnalyticsService $analyticsService
    ) {
        $this->analyticsService = $analyticsService;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var MagentoOrder $order */
        $order = $observer->getData('order');

        $this->analyticsService->handleOrder($order);
    }
}
