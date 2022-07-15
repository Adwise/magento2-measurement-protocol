<?php

namespace Adwise\Analytics\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

class SaveGaClientIdToOrderObserver implements ObserverInterface
{

    /** @var QuoteRepository */
    protected $_quoteRepository;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * SaveGaClientIdToOrderObserver constructor.
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->_quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var Order $order */
        $order = $observer->getOrder();

        try {
            $quote = $this->_quoteRepository->get($order->getQuoteId());
            $order->setGaClientId($quote->getGaClientId());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        } finally {
            return $this;
        }
    }

}
