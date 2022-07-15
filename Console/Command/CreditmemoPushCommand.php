<?php

namespace Adwise\Analytics\Console\Command;

use Adwise\Analytics\Service\AnalyticsService;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Adwise\Analytics\Helper\Creditmemo;
use Adwise\Analytics\Helper\Data as DataHelper;

class CreditmemoPushCommand extends Command
{

    /** @var State */
    private $state;

    /** @var CreditMemo */
    protected $creditMemoHelper;

    /** @var DataHelper */
    protected $dataHelper;

    /** @var AnalyticsService */
    protected $analyticsService;

    /**
     * OrderPushCommand constructor.
     * @param State $state
     * @param DataHelper $dataHelper
     * @param CreditMemo $creditmemoHelper
     * @param AnalyticsService $analyticsService
     */
    public function __construct(
        State $state,
        DataHelper $dataHelper,
        CreditMemo $creditmemoHelper,
        AnalyticsService $analyticsService
    ) {
        $this->state = $state;
        $this->dataHelper = $dataHelper;
        $this->creditMemoHelper = $creditmemoHelper;
        $this->analyticsService = $analyticsService;
        parent::__construct();
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName("adwise:analytics:pushcreditmemo")
            ->setDescription("Push creditmemo to Google Analytics")
            ->addArgument("id", InputArgument::REQUIRED, "Magento creditmemo ID to push");
    }

    /**
     * @param InputInterface $inp
     * @param OutputInterface $out
     * @return bool
     */
    public function execute(InputInterface $inp, OutputInterface $out)
    {
        $this->state->setAreaCode(Area::AREA_ADMINHTML);

        $creditmemoId = $inp->getArgument('id');
        $creditmemo = $this->creditMemoHelper->getCreditmemoById($creditmemoId);

        if (!$creditmemo->getEntityId()) {
            $out->writeln('<error>No creditmemo found with ID ' . $creditmemoId . ' </error>');

            return false;
        }

        $this->dataHelper->setStoreId($creditmemo->getStoreId());
        if (!$this->dataHelper->getIsEnabled()) {
            $out->writeln('<error>Adwise_Analytics not enabled in configuration. Aborting.</error>');

            return false;
        }

        try {
            $order = $creditmemo->getOrder();
            $this->analyticsService->handleCreditmemo($creditmemo);

            $order->addStatusHistoryComment('Adwise_Analytics: Sent creditmemo (ID: ' . $creditmemoId . ')');
            $out->writeln('<info>Creditmemo (ID: ' . $creditmemoId . ') succesfully pushed to Google Analytics</info>');
        } catch (\Exception $e) {
            $out->writeln('<error>Creditmemo (ID: ' . $creditmemoId . ') not sent. Error: ' . $e . '</error>');
        }
    }
}
