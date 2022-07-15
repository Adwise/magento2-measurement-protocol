<?php

namespace Adwise\Analytics\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Model\Order as MagentoOrder;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

class Creditmemo extends Order
{
    /** @var CreditmemoRepositoryInterface */
    protected $creditmemoRepository;

    /**
     * Creditmemo constructor.
     * @param Data $dataHelper
     * @param Product $productHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param CollectionFactoryInterface $orderCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     */
    public function __construct(
        Data $dataHelper,
        Product $productHelper,
        OrderRepositoryInterface $orderRepository,
        CollectionFactoryInterface $orderCollectionFactory,
        ProductRepositoryInterface $productRepository,
        CreditmemoRepositoryInterface $creditmemoRepository
    ) {
        parent::__construct($dataHelper, $productHelper, $orderRepository, $orderCollectionFactory, $productRepository);
        $this->creditmemoRepository = $creditmemoRepository;
    }

    /**
     * @param $id
     * @return CreditmemoInterface
     */
    public function getCreditmemoById($id)
    {
        return $this->creditmemoRepository->get($id);
    }
}
