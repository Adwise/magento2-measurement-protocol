<?php

namespace Adwise\Analytics\Block\Adminhtml\Config\Debug;

use Magento\Framework\View\Element\Template;
use Adwise\Analytics\Model\CreditMemoDataProviders;
use Adwise\Analytics\Model\OrderDataProviders;
use Magento\Framework\View\Element\Template\Context;

class IncompatiblePluginList extends Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    protected $_template = 'Adwise_Analytics::config/debug/incompatible_plugin_list.phtml';
    private OrderDataProviders $orderDataProvider;
    private CreditMemoDataProviders $creditMemoProvider;

    public function __construct(
        Context $context,
        OrderDataProviders $orderDataProviders,
        CreditMemoDataProviders $creditMemoProviders,
        array $data = []
    ) {
        $this->orderDataProvider = $orderDataProviders;
        $this->creditMemoProvider = $creditMemoProviders;
        Template::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        /**
         * @noinspection PhpUndefinedMethodInspection
         */
        $this->setElement($element);

        return $this->toHtml();
    }

    public function getIncompatiblePlugins()
    {
        $incompatiblePlugins = [];
        $orderDataProvider = $this->readPrivateProperty($this->orderDataProvider, 'orderDataProviders');
        $creditMemoDataProvider = $this->readPrivateProperty($this->creditMemoProvider, 'creditMemoDataProviders');
        $incompatiblePlugins = array_merge(array_keys($orderDataProvider), array_keys($creditMemoDataProvider));
        return $incompatiblePlugins;
    }

    public function readPrivateProperty(mixed $object, string $property): mixed
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($object);
    }


}
