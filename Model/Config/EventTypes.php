<?php

namespace Adwise\Analytics\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class EventTypes implements ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'event',
                'label' => 'Event',
            ],
            [
                'value' => 'pageview',
                'label' => 'Pageview'
            ]
        ];
    }
}
