<?php

namespace Adwise\Analytics\Model\DataProviders\Order;

use Adwise\Analytics\Api\OrderDataProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Helper\Product;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as MagentoOrder;

class ProductCategoryProvider implements OrderDataProviderInterface
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
        $i = 1;

        foreach ($products as $product) {
            if ($product->getParentItemId()) {
                continue;
            }

            $fullProduct = $this->productHelper->getProductBySku($product->getSku());
            if ($fullProduct) {
                $categories = $this->productHelper->getProductCategories($fullProduct);
                $item = [];
                $i = 1;
                foreach ($categories as $category) {
                    if ($i === 1) {
                        $item['item_category'] = $category;
                    } else {
                        $item['item_category' . $i] = $category;
                    }
                    if ($i === 5) {
                        break;
                    }
                    $i++;
                }
            }

            $data['items'][$product->getSku()] = $item;
        }

        return $data;
    }

    public function getData(OrderInterface $order)
    {
        return $this->mapHitProducts($order->getItems());
    }
}
