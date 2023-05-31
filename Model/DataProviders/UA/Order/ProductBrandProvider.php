<?php

namespace Adwise\Analytics\Model\DataProviders\UA\Order;

use Adwise\Analytics\Api\OrderDataProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Helper\Product;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as MagentoOrder;

class ProductBrandProvider implements OrderDataProviderInterface
{
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var Product
     */
    private $productHelper;

    /**
     * AnalyticsMapper constructor.
     * @param Data $dataHelper
     * @param Product $productHelper
     */
    public function __construct(
        Data $dataHelper,
        Product $productHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->productHelper = $productHelper;
    }

    public function mapHitProducts($products)
    {
        $data = [];
        $i = 1;

        foreach ($products as $product) {
            if ($product->getParentItemId()) {
                continue;
            }

            $fullProduct = $this->productHelper->getProductBySku($product->getSku());
            if ($fullProduct) {
                $data['pr' . $i . 'br'] = $fullProduct->getData($this->dataHelper->getBrandAttribute()) ? $fullProduct->getAttributeText($this->dataHelper->getBrandAttribute()) : $this->dataHelper->getDefaultBrand();
            }
            ++$i;
        }

        return $data;
    }

    public function getData(OrderInterface $order)
    {
        return $this->mapHitProducts($order->getItems());
    }
}
