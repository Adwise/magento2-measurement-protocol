<?php

namespace Adwise\Analytics\Console\Command;

use Adwise\Analytics\Service\AnalyticsService;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Adwise\Analytics\Helper\Order as OrderHelper;
use Adwise\Analytics\Helper\Data as DataHelper;

class OrderPushCommand extends Command
{

    /** @var State */
    private $state;

    /** @var OrderHelper */
    protected $orderHelper;

    /** @var DataHelper */
    protected $dataHelper;

    /** @var AnalyticsService */
    protected $analyticsService;

    /**
     * OrderPushCommand constructor.
     * @param State $state
     * @param DataHelper $dataHelper
     * @param OrderHelper $orderHelper
     * @param AnalyticsService $analyticsService
     */
    public function __construct(
        State $state,
        DataHelper $dataHelper,
        OrderHelper $orderHelper,
        AnalyticsService $analyticsService
    ) {
        $this->state = $state;
        $this->dataHelper = $dataHelper;
        $this->orderHelper = $orderHelper;
        $this->analyticsService = $analyticsService;
        parent::__construct();
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName("adwise:analytics:pushorder")
            ->setDescription("Push order to Google Analytics")
            ->addArgument("incrementId", InputArgument::REQUIRED, "Magento order increment ID to push")
            ->addArgument("undo", InputArgument::OPTIONAL, "Make the amounts negative (to undo sending to Google)",
                false);
    }

    /**
     * @param InputInterface $inp
     * @param OutputInterface $out
     * @return bool
     */
    public function execute(InputInterface $inp, OutputInterface $out)
    {
        $this->state->setAreaCode(Area::AREA_ADMINHTML);

        $incrementId = $inp->getArgument('incrementId');
        $order = $this->orderHelper->getOrderByIncrementId($incrementId);

        if (!$order) {
            $out->writeln('<error>No order found with increment ID ' . $incrementId . ' </error>');

            return false;
        }

        $this->dataHelper->setStoreId($order->getStoreId());
        if (!$this->dataHelper->getIsEnabled()) {
            $out->writeln('<error>Adwise_Analytics not enabled in configuration. Aborting.</error>');

            return false;
        }

        try {
            $this->analyticsService->handleOrder($order);
        } catch (\Exception $e) {
            $out->writeln('<error>Data not sent. Error: ' . $e . '</error>');
        }
    }
}
