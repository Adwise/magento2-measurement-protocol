<?php

namespace Adwise\Analytics\Model\Checkout;

use Adwise\Analytics\Helper\Data as DataHelper;
use Magento\Checkout\Model\ConfigProviderInterface;

class AdditionalConfigProvider implements ConfigProviderInterface
{

    /** @var DataHelper */
    private $_dataHelper;

    /**
     * AdditionalConfigProvider constructor.
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        $output['tracking_id'] = $this->_dataHelper->getTrackingId();

        return $output;
    }
}
