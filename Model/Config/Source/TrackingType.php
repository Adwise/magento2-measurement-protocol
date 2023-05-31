<?php
namespace Adwise\Analytics\Model\Config\Source;

class TrackingType implements \Magento\Framework\Option\ArrayInterface
{
    const TRACKING_TYPE_DATALAYER = 'datalayer';
    const TRACKING_TYPE_GTAG = 'gtag';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => self::TRACKING_TYPE_DATALAYER, 'label' => __('Datalayer')], ['value' => self::TRACKING_TYPE_GTAG, 'label' => __('GTAG')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::TRACKING_TYPE_DATALAYER => __('Datalayer'), self::TRACKING_TYPE_GTAG => __('GTAG')];
    }
}
