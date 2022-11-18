<?php

namespace Adwise\Analytics\Block\Html;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class DataLayer extends Template implements \Magento\Framework\View\Element\BlockInterface
{
    const CONFIG_PATH_TRACKING_TYPE = 'adwise_analytics/frontend/tracking_type';
    protected $_template = 'Adwise_Analytics::datalayer.phtml';

    public function getTrackingType() {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_TRACKING_TYPE, ScopeInterface::SCOPE_WEBSITE);
    }

}
