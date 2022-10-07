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
        return (bool) $this->getConfig(self::XML_IS_ENABLED);
    }

    /**
     * @return string
     */
    public function getCidCookieName()
    {
        return (string) $this->getConfig(self::CID_COOKIE_NAME) ?? '_ga';
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
     * @return bool
     * @deprecated
     */
    public function getIsOrderHitEnabled()
    {
        return (bool) $this->getConfig(self::XML_IS_ORDER_HIT_ENABLED);
    }

    /**
     * @return bool
     * @deprecated
     */
    public function getIsCreditMemoHitEnabled()
    {
        return (bool) $this->getConfig(self::XML_IS_CREDIT_MEMO_HIT_ENABLED);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getCreditMemoEventAction()
    {
        return $this->getConfig(self::XML_CREDIT_MEMO_EVENT_ACTION);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getCreditMemoProductAction()
    {
        return $this->getConfig(self::XML_CREDIT_MEMO_PRODUCT_ACTION);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getHitType()
    {
        return $this->getConfig(self::XML_HIT_TYPE);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getPageviewHostname()
    {
        return $this->getConfig(self::XML_PAGEVIEW_HOSTNAME);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getPageviewPath()
    {
        return $this->getConfig(self::XML_PAGEVIEW_PATH);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getPageviewTitle()
    {
        return $this->getConfig(self::XML_PAGEVIEW_TITLE);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getEventCategory()
    {
        return $this->getConfig(self::XML_EVENT_CATEGORY);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getEventAction()
    {
        return $this->getConfig(self::XML_EVENT_ACTION);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getEventLabel()
    {
        return $this->getConfig(self::XML_EVENT_LABEL);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getEventValue()
    {
        return $this->getConfig(self::XML_EVENT_VALUE);
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function getTrackingId()
    {
        return $this->getConfig(self::XML_TRACKING_ID);
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function getGaCollectUrl()
    {
        return $this->getConfig(self::GA_COLLECT_URL);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getUserAgent()
    {
        return $this->getConfig(self::USER_AGENT);
    }

    /**
     * @return mixed
     *
     */
    public function getCustomDimensionKnown()
    {
        return $this->getConfig(self::CUSTOM_DIMENSION_KNOWN);
    }

    public function getCustomDimensionTimestamp()
    {
        return $this->getConfig(self::CUSTOM_DIMENSION_TIMESTAMP);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getDefaultBrand()
    {
        return $this->getConfig(self::XML_PATH_DEFAULT_BRAND);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getBrandAttribute()
    {
        return $this->getConfig(self::XML_PATH_BRAND_ATTRIBUTE);
    }

    /**
     * @return mixed
     * @deprecared
     */
    public function getTransactionAffiliation()
    {
        return $this->getConfig(self::TRANSACTION_AFFILIATION);
    }

    /**
     * @return array
     * @deprecared
     */
    public function getIgnoredCategories()
    {
        $value = $this->getConfig(self::IGNORED_CATEGORIES);

        if ($value) {
            return explode(',', $value);
        }

        return [];
    }

    /**
     * @return bool
     */
    public function getIsDebugLoggingEnabled()
    {
        return (bool) $this->getConfig(self::XML_DEBUG_PAYLOAD_LOGGING_ENABLED);
    }
}
