<?php

namespace Adwise\Analytics\Model\DataProviders\MP\Order;

use Adwise\Analytics\Api\MeasurementProtocol\OrderDataProviderInterface;
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
        $data = ['items' => []];

        foreach ($products as $product) {
            if ($product->getParentItemId()) {
                continue;
            }

            $fullProduct = $this->productHelper->getProductBySku($product->getSku());

            if (!$fullProduct) {
                continue;
            }

            $item = [
                'item_brand' => $fullProduct->getData($this->dataHelper->getBrandAttribute()) ? $fullProduct->getAttributeText($this->dataHelper->getBrandAttribute()) : $this->dataHelper->getDefaultBrand()
            ];
            $item = array_filter($item);
            $data['items'][$product->getSku()] = $item;
        }

        return $data;
    }

    public function getData(OrderInterface $order)
    {
        return $this->mapHitProducts($order->getItems());
    }
}
