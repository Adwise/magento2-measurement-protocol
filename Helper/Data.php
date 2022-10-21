<?php

namespace Adwise\Analytics\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data
{
    /**
     * XML CONFIG PATHS
     */
    const XML_PATH_MODULE_IS_ENABLED = 'adwise_analytics/general/is_enabled';
    const XML_PATH_BRAND_ATTRIBUTE = 'adwise_analytics/general/brand_attribute';
    const XML_PATH_CID_COOKIE_NAME = 'adwise_analytics/general/cid_cookie';
    const XML_PATH_DEFAULT_BRAND = 'adwise_analytics/general/default_brand';
    const XML_PATH_IGNORED_CATEGORIES = 'adwise_analytics/general/ignored_categories';
    const XML_PATH_MEASUREMENT_PROTOCOL_ENABLED = 'adwise_analytics/mp/is_enabled';
    const XML_PATH_MEASUREMENT_PROTOCOL_MEASUREMENT_ID = 'adwise_analytics/mp/measurement_id';
    const XML_PATH_MEASUREMENT_PROTOCOL_API_SECRET = 'adwise_analytics/mp/api_secret';
    const XML_PATH_MEASUREMENT_PROTOCOL_ENDPOINT = 'adwise_analytics/mp/endpoint';
    const XML_PATH_MEASUREMENT_PROTOCOL_PURCHASE_ENABLED = 'adwise_analytics/mp/purchase/is_enabled';
    const XML_PATH_MEASUREMENT_PROTOCOL_REFUND_ENABLED = 'adwise_analytics/mp/refund/is_enabled';
    const XML_PATH_DEBUG_LOGGING = 'adwise_analytics/debug/payload_logging';

    protected $storeId = 0;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /**
     * Data constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $storeID = $this->storeManager->getStore()->getStoreId();
        $this->setStoreId($storeID);
    }

    /**
     * @param $id
     * @return $this
     */
    public function setStoreId($id)
    {
        $this->storeId = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return (bool) $this->getConfig(self::XML_PATH_MODULE_IS_ENABLED);
    }

    /**
     * @return string
     */
    public function getCidCookieName()
    {
        return (string) $this->getConfig(self::XML_PATH_CID_COOKIE_NAME) ?? '_ga';
    }

    public function getMPEnabled(): bool
    {
        return (bool) $this->getConfig(self::XML_PATH_MEASUREMENT_PROTOCOL_ENABLED);
    }

    public function getMPMeasurementId(): string
    {
        return (string) $this->getConfig(self::XML_PATH_MEASUREMENT_PROTOCOL_MEASUREMENT_ID);
    }

    public function getMPApiSecret(): string
    {
        return (string) $this->getConfig(self::XML_PATH_MEASUREMENT_PROTOCOL_API_SECRET);
    }

    public function getMPEndpoint(): string
    {
        return (string) $this->getConfig(self::XML_PATH_MEASUREMENT_PROTOCOL_ENDPOINT);
    }

    public function getMPPurchaseEventEnabled(): bool
    {
        return (bool) $this->getConfig(self::XML_PATH_MEASUREMENT_PROTOCOL_PURCHASE_ENABLED);
    }

    public function getMPRefundEventEnabled(): bool
    {
        return (bool) $this->getConfig(self::XML_PATH_MEASUREMENT_PROTOCOL_REFUND_ENABLED);
    }

    /**
     * @return mixed
     */
    public function getDefaultBrand()
    {
        return $this->getConfig(self::XML_PATH_DEFAULT_BRAND);
    }

    /**
     * @return mixed
     */
    public function getBrandAttribute()
    {
        return $this->getConfig(self::XML_PATH_BRAND_ATTRIBUTE);
    }

    /**
     * @return array
     */
    public function getIgnoredCategories(): array
    {
        $value = $this->getConfig(self::XML_PATH_IGNORED_CATEGORIES);

        if ($value) {
            return explode(',', $value);
        }

        return [];
    }

    public function getIsDebugLoggingEnabled(): bool
    {
        return (bool) $this->getConfig(self::XML_PATH_DEBUG_LOGGING);
    }

    public function getUserAgent(): string {
        return 'AdwiseAnalytics/2.0.0';
    }
}
