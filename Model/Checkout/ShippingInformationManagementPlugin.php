<?php

namespace Adwise\Analytics\Model\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

class ShippingInformationManagementPlugin
{
    /** @var QuoteRepository */
    protected $_quoteRepository;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * ShippingInformationManagementPlugin constructor.
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
     * @param ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        if (!empty($extAttributes)) {
            try {
                if ($gaClientId = $extAttributes->getGaClientId()) {
                    $quote = $this->_quoteRepository->getActive($cartId);
                    $quote->setGaClientId($gaClientId);
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
