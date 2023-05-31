<?php

namespace Adwise\Analytics\Model\DataProviders\UA\Creditmemo;

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
        $i = 1;

        foreach ($products as $product) {
            if ($product->getParentItemId()) {
                continue;
            }

            $data['pr' . $i . 'id'] = $product->getSku();
            $data['pr' . $i . 'qt'] = $this->getProductQty($product);

            ++$i;
        }

        return $data;
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
        $qty = $product->getQtyOrdered() ? $product->getQtyOrdered() : $product->getQty();
        return (int)$qty;
    }
}
