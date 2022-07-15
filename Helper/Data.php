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
    const XML_IS_ENABLED = 'adwise_analytics/general/is_enabled';

    const XML_TRACKING_ID = 'adwise_analytics/general/tracking_id';

    const BRAND_ATTRIBUTE = 'adwise_analytics/general/brand_attribute';

    const CID_COOKIE_NAME = 'adwise_analytics/general/cid_cookie';

    const DEFAULT_BRAND = 'adwise_analytics/general/default_brand';

    const IGNORED_CATEGORIES = 'adwise_analytics/general/ignored_categories';

    const XML_HIT_TYPE = 'adwise_analytics/hit/type';

    const XML_PAGEVIEW_HOSTNAME = 'adwise_analytics/hit/pageview_hostname';

    const XML_PAGEVIEW_PATH = 'adwise_analytics/hit/pageview_path';

    const XML_PAGEVIEW_TITLE = 'adwise_analytics/hit/pageview_title';

    const XML_EVENT_CATEGORY = 'adwise_analytics/hit/event_category';

    const XML_EVENT_ACTION = 'adwise_analytics/hit/event_action';

    const XML_EVENT_LABEL = 'adwise_analytics/hit/event_label';

    const XML_EVENT_VALUE = 'adwise_analytics/hit/event_value';

    const GA_COLLECT_URL = 'adwise_analytics/hit/ga_collect_url';

    const USER_AGENT = 'adwise_analytics/hit/user_agent';

    const CUSTOM_DIMENSION_KNOWN = 'adwise_analytics/hit/cd_known';

    const CUSTOM_DIMENSION_TIMESTAMP = 'adwise_analytics/hit/cd_timestamp';

    const TRANSACTION_AFFILIATION = 'adwise_analytics/hit/transaction_affiliation';

    const XML_IS_ORDER_HIT_ENABLED = 'adwise_analytics/hit_order/is_enabled';

    const XML_IS_CREDIT_MEMO_HIT_ENABLED = 'adwise_analytics/hit_credit_memo/is_enabled';

    const XML_CREDIT_MEMO_EVENT_ACTION = 'adwise_analytics/hit_credit_memo/event_action';

    const XML_CREDIT_MEMO_PRODUCT_ACTION = 'adwise_analytics/hit_credit_memo/product_action';

    const XML_DEBUG_PAYLOAD_LOGGING_ENABLED = 'adwise_analytics/debug/payload_logging';

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

    /**
     * @return bool
     */
    public function getIsOrderHitEnabled()
    {
        return (bool) $this->getConfig(self::XML_IS_ORDER_HIT_ENABLED);
    }

    /**
     * @return bool
     */
    public function getIsCreditMemoHitEnabled()
    {
        return (bool) $this->getConfig(self::XML_IS_CREDIT_MEMO_HIT_ENABLED);
    }

    public function getCreditMemoEventAction()
    {
        return $this->getConfig(self::XML_CREDIT_MEMO_EVENT_ACTION);
    }

    public function getCreditMemoProductAction()
    {
        return $this->getConfig(self::XML_CREDIT_MEMO_PRODUCT_ACTION);
    }

    /**
     * @return mixed
     */
    public function getHitType()
    {
        return $this->getConfig(self::XML_HIT_TYPE);
    }

    /**
     * @return mixed
     */
    public function getPageviewHostname()
    {
        return $this->getConfig(self::XML_PAGEVIEW_HOSTNAME);
    }

    /**
     * @return mixed
     */
    public function getPageviewPath()
    {
        return $this->getConfig(self::XML_PAGEVIEW_PATH);
    }

    /**
     * @return mixed
     */
    public function getPageviewTitle()
    {
        return $this->getConfig(self::XML_PAGEVIEW_TITLE);
    }

    /**
     * @return mixed
     */
    public function getEventCategory()
    {
        return $this->getConfig(self::XML_EVENT_CATEGORY);
    }

    /**
     * @return mixed
     */
    public function getEventAction()
    {
        return $this->getConfig(self::XML_EVENT_ACTION);
    }

    /**
     * @return mixed
     */
    public function getEventLabel()
    {
        return $this->getConfig(self::XML_EVENT_LABEL);
    }

    /**
     * @return mixed
     */
    public function getEventValue()
    {
        return $this->getConfig(self::XML_EVENT_VALUE);
    }

    /**
     * @return mixed
     */
    public function getTrackingId()
    {
        return $this->getConfig(self::XML_TRACKING_ID);
    }

    /**
     * @return mixed
     */
    public function getGaCollectUrl()
    {
        return $this->getConfig(self::GA_COLLECT_URL);
    }

    /**
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->getConfig(self::USER_AGENT);
    }

    /**
     * @return mixed
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
     */
    public function getDefaultBrand()
    {
        return $this->getConfig(self::DEFAULT_BRAND);
    }

    /**
     * @return mixed
     */
    public function getBrandAttribute()
    {
        return $this->getConfig(self::BRAND_ATTRIBUTE);
    }

    /**
     * @return mixed
     */
    public function getTransactionAffiliation()
    {
        return $this->getConfig(self::TRANSACTION_AFFILIATION);
    }

    /**
     * @return array
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
