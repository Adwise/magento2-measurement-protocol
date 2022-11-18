<?php

namespace Adwise\Analytics\Block\Html;

use Magento\Framework\View\Element\Template;

class Head extends Template implements \Magento\Framework\View\Element\BlockInterface
{
    const CONFIG_PATH_HEAD_CONTENT = 'adwise_analytics/frontend/head_config';
    protected $_template = 'Adwise_Analytics::head.phtml';

    public function getHeadContent() {
        return $this->_scopeConfig->getValue(self::CONFIG_PATH_HEAD_CONTENT);
    }
}
