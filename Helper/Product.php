<?php

namespace Adwise\Analytics\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class Product
{
    protected $productRepository;

    protected $categoryCollectionFactory;

    protected $dataHelper;

    public function __construct(
        ProductRepository $productRepository,
        CollectionFactory $categoryCollectionFactory,
        Data $dataHelper
    ) {
        $this->productRepository = $productRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param $productSku
     * @return ProductInterface|MagentoProduct|null
     */
    public function getProductBySku($productSku) {
        try {
            return $this->productRepository->get($productSku);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $product
     * @return array
     */
    public function getProductCategories($product) {
        if (!$product) {
            return [];
        }

        if(!($product->getCategoryIds())){
            return [];
        }

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('entity_id', $product->getCategoryIds() ?? []);

        $ignoredCategories = $this->dataHelper->getIgnoredCategories();

        $result = [];

        foreach ($collection as $category) {
            if (!in_array($category->getId(), $ignoredCategories)) {
                $result[] = $category->getName();
            }
        }

        return $result;
    }
}
