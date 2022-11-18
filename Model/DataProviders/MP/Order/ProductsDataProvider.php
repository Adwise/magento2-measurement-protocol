<?php

namespace Adwise\Analytics\Model\DataProviders\MP\Order;

use Adwise\Analytics\Api\MeasurementProtocol\OrderDataProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Helper\Product;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as MagentoOrder;

class ProductsDataProvider implements OrderDataProviderInterface
{

    private $undo = false;

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

    /**
     * @param OrderItemInterface[] $products
     * @return array[]
     */
    public function mapHitProducts($products)
    {
        $data = [];

        /**
         * @var MagentoOrder\Item $product
         */
        foreach ($products as $product) {
            if ($product->getParentItemId()) {
                continue;
            }

            $fullProduct = $this->productHelper->getProductBySku($product->getSku());

            $item = [
                'item_id' => $product->getSku(),
                'item_name' => $product->getName(),
            ];

            if ($product->getDiscountAmount()) {
                $item['discount'] = $this->round($product->getDiscountAmount());
            }

            $item['price'] = $this->round($product->getPrice());
            $item['quantity'] = $this->round($this->getProductQty($product));

            $data[$product->getSku()] = $item;
        }

        return ['items' => $data];
    }

    public function getData(OrderInterface $order)
    {
        $this->undo = $order->getStatus() === MagentoOrder::STATE_CANCELED;

        return $this->mapHitProducts($order->getItems());
    }


    /**
     * @param $product
     * @return int
     */
    public function getProductQty($product)
    {
        $qty = $product->getQtyOrdered() ? $product->getQtyOrdered() : $product->getQty();
        return $this->undo ? -(int)$qty : $qty;
    }

    private function round($number){
        return round($number, 4, PHP_ROUND_HALF_EVEN);
    }
}
