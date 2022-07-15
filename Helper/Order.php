<?php

namespace Adwise\Analytics\Helper;

use Adwise\Analytics\Service\AnalyticsMapper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order as MagentoOrder;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

class Order
{
    /** @var Data */
    protected $dataHelper;

    /** @var Product */
    protected $productHelper;

    /** @var MagentoOrder */
    protected $order;

    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    /** @var CollectionFactoryInterface */
    protected $orderCollectionFactory;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /**
     * Order constructor.
     * @param Data $dataHelper
     * @param Product $productHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param CollectionFactoryInterface $orderCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Data $dataHelper,
        Product $productHelper,
        OrderRepositoryInterface $orderRepository,
        CollectionFactoryInterface $orderCollectionFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->dataHelper = $dataHelper;
        $this->orderRepository = $orderRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $id
     *
     * @return OrderInterface
     */
    public function getOrderById($id)
    {
        return $this->orderRepository->get($id);
    }

    /**
     * @param $incrementId
     * @return OrderInterface|null
     */
    public function getOrderByIncrementId($incrementId)
    {
        /** @var Collection $collection */
        $collection = $this->orderCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('increment_id', ['eq' => $incrementId]);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        }

        return null;
    }
}
