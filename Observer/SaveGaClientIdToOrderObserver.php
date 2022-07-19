<?php

namespace Adwise\Analytics\Observer;

use Adwise\Analytics\Helper\Data;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;


class SaveGaClientIdToOrderObserver implements ObserverInterface
{
    /** @var CookieManagerInterface */
    protected $_cookieManager;

    /** @var QuoteRepository */
    protected $_quoteRepository;

    /** @var LoggerInterface */
    protected $logger;

    /** @var Data */
    protected $dataHelper;

    /**
     * SaveGaClientIdToOrderObserver constructor.
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        LoggerInterface $logger,
        CookieManagerInterface $cookieManager,
        Data $dataHelper
    ) {
        $this->_quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->_cookieManager = $cookieManager;
        $this->dataHelper = $dataHelper;
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
            $order->setGaClientId($this->getGaClientIdFromCookie());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        } finally {
            return $this;
        }
    }

    public function getGaClientIdFromCookie(): ?string
    {
        $cookieValue = $this->_cookieManager->getCookie($this->dataHelper->getCidCookieName());

        if (!$cookieValue) {
            return null;
        }

        $parts = explode('.', $cookieValue);
        if (count($parts) !== 4) {
            return null;
        }

        return implode('.', [$parts[2], $parts[3]]);
    }
}
