<?php

namespace Adwise\Analytics\Console\Command;

use Adwise\Analytics\Helper\Creditmemo;
use Adwise\Analytics\Model\MP\CreditMemoDataProviders;
use Adwise\Analytics\Model\MP\OrderDataProviders;
use Adwise\Analytics\Helper\Order;
use Adwise\Analytics\Helper\Data as DataHelper;
use Adwise\Analytics\Service\AnalyticsService;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GetPayloadCommand extends Command
{
    /** @var State */
    private State $state;
    private Creditmemo $creditMemoHelper;
    private Order $orderHelper;
    private OrderDataProviders $orderDataProviders;
    private CreditMemoDataProviders $creditMemoDataProviders;

    public function __construct(
        State $state,
        CreditMemo $creditmemoHelper,
        Order $orderHelper,
        OrderDataProviders $orderDataProviders,
        CreditMemoDataProviders $creditMemoDataProviders
    ) {
        $this->state = $state;
        $this->creditMemoHelper = $creditmemoHelper;
        $this->orderHelper = $orderHelper;
        $this->orderDataProviders = $orderDataProviders;
        $this->creditMemoDataProviders = $creditMemoDataProviders;

        parent::__construct();
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName("adwise:analytics:debug")
            ->setDescription("Output payload for a order or creditmemo")
            ->addOption('creditmemo', 'c', InputArgument::OPTIONAL, 'indicate if this id is for a creditmemo')
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

        $id = $inp->getArgument('id');

        if ($inp->getOption('creditmemo')) {
            $creditmemo = $this->creditMemoHelper->getCreditmemoById($id);
            $data = $this->creditMemoDataProviders->getData($creditmemo);
        } else {
            $order = $this->orderHelper->getOrderById($id);
            $data = $this->orderDataProviders->getData($order);
        }

        $out->writeln(json_encode($data, JSON_PRETTY_PRINT));
    }
}

