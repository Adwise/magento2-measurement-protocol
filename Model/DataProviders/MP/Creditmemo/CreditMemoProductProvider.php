<?php

namespace Adwise\Analytics\Model\DataProviders\MP\Creditmemo;

use Adwise\Analytics\Api\CreditMemoProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Helper\Product;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as MagentoOrder;

class CreditMemoProductProvider implements CreditMemoProviderInterface
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
    public function getData(CreditmemoInterface $creditmemo)
    {
        return $this->mapHitProducts($creditmemo->getItems());
    }


    /**
     * @param $product
     * @return int
     */
    public function getProductQty($product)
    {
        $qty = $product->getQtyRefunded() ? $product->getQtyRefunded() : $product->getQty();
        return (int)$qty;
    }
}
