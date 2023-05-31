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
            $order->setGaSessionId($this->getGaSessionIdFromCookie());
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

    public function getGaSessionIdFromCookie(): ?string
    {
        // session id cookie is _ga_(container)
        if ((!$this->dataHelper->getMPEnabled()) || empty($this->dataHelper->getMPMeasurementId())) {
            return null;
        }

        $measurementId = $this->dataHelper->getMPMeasurementId();
        // MeasurementID is G-123WXVRLKN, container is 123WXVRLKN
        $container = substr($measurementId, 2);
        $cookieValue = $this->_cookieManager->getCookie('_ga_' . $container);
        $parts = explode('.', $cookieValue ?? '');
        // CookieValue is GS1.1.1677231346.2.1.1677233939.0.0.0, session id is 1677231346
        if (count($parts) < 3) {
            return null;
        }

        return $parts[2];
    }
}
