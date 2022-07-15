<?php

namespace Adwise\Analytics\Model\DataProviders\Order;

use Adwise\Analytics\Api\OrderDataProviderInterface;
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

            $data['pr' . $i . 'id'] = $product->getSku();
            $data['pr' . $i . 'nm'] = $product->getName();
            $data['pr' . $i . 'pr'] = $product->getPrice();
            $data['pr' . $i . 'qt'] = $this->getProductQty($product);

            ++$i;
        }

        return $data;
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
}
