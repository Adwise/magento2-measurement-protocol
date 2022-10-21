<?php

use Adwise\Analytics\Service\AnalyticsCaller;
use Adwise\Analytics\Helper\Order as OrderHelper;
use Adwise\Analytics\Helper\Creditmemo as CreditmemoHelper;
use Adwise\Analytics\Helper\Data as DataHelper;
use Magento\Sales\Model\Order as MagentoOrder;
use Adwise\Analytics\Service\AnalyticsService;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Model\Order\Creditmemo;

class AnalyticsServiceTest extends PHPUnit\Framework\TestCase
{

    /**
     * @var AnalyticsCaller|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $analyticsCallerMock;

    /**
     * @var OrderHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $orderHelperMock;
    /**
     * @var CreditmemoHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $creditmemoMock;

    /**
     * @var DataHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $datahelperMock;

    /**
     * @var AnalyticsService
     */
    protected $analyticsService;

    protected function setUp(): void
    {
        $this->analyticsCallerMock = $this->getMockBuilder(AnalyticsCaller::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderHelperMock = $this->getMockBuilder(OrderHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->creditmemoMock = $this->getMockBuilder(CreditmemoHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->datahelperMock = $this->getMockBuilder(DataHelper::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->analyticsService = (new ObjectManager($this))->getObject(
            AnalyticsService::class,
            [
                'dataHelper' => $this->datahelperMock,
                'analyticsCaller' => $this->analyticsCallerMock
            ]
        );
    }

    /**
     * Configures dataHelper mock to imitate specific config settings
     * @param $enableModule
     * @param $enableOrderHit
     * @param $enableCreditMemoHit
     */
    private function enableSettings($enableModule, $enableOrderHit, $enableCreditMemoHit){
        $this->datahelperMock->expects($this->any())->method('getIsEnabled')->willReturn($enableModule);
        $this->datahelperMock->expects($this->any())->method('getMPEnabled')->willReturn($enableModule);
        $this->datahelperMock->expects($this->any())->method('getMPPurchaseEventEnabled')->willReturn($enableOrderHit);
        $this->datahelperMock->expects($this->any())->method('getMPRefundEventEnabled')->willReturn($enableCreditMemoHit);
    }

    /**
     *  Test if a PENDING_PAYMENT is not exported to Google
     */
    public function testPendingOrderNotProcessed(): void
    {
        $this->enableSettings(true,true,true);
        $order = $this->getMockedOrderWithState(MagentoOrder::STATE_PENDING_PAYMENT);

        $this->analyticsCallerMock->expects($this->never())->method('handleOrder');
        $this->assertEquals(AnalyticsService::ERROR_UNKNOWN_ORDER_STATE, $this->analyticsService->handleOrder($order));
    }

    /**
     * Test if a processing order, that has never been exported before, is successfully exported
     */
    public function testProcessingOrderNeverExported(): void
    {
        $this->enableSettings(true,true,true);
        $order = $this->getMockedOrderWithState(MagentoOrder::STATE_PROCESSING);

        //This is the first time we have exported this order
        $order->expects($this->atLeastOnce())->method('getAwAnalyticsExport')->willReturn(null);
        $order->expects($this->once())->method('setAwAnalyticsExport')->with(AnalyticsService::EXPORT_STATUS_PROCESSED);
        $order->expects($this->once())->method('addCommentToStatusHistory');
        $order->expects($this->once())->method('save');

        $this->analyticsCallerMock->expects($this->once())->method('handleOrder')->with($order);
        $this->assertEquals(AnalyticsService::SUCCESS, $this->analyticsService->handleOrder($order));
    }

    /**
     * Test if other states are never exported
     */
    public function testInvalidProcessingStates(){
        $invalidStates = [
            AnalyticsService::EXPORT_STATUS_OLD_EXPORTED_POSITIVE,
            AnalyticsService::EXPORT_STATUS_OLD_EXPORTED_NEGATIVE,
            AnalyticsService::EXPORT_STATUS_PROCESSED,
            AnalyticsService::EXPORT_STATUS_NEW_CANCELLED,
            AnalyticsService::EXPORT_STATUS_FULLY_REFUNDED,
        ];

        $this->enableSettings(true,true,true);

        $this->analyticsCallerMock->expects($this->never())->method('handleOrder');

        foreach($invalidStates as $state){
            $order = $this->getMockedOrderWithState(MagentoOrder::STATE_PROCESSING);
            $order->expects($this->atLeastOnce())->method('getAwAnalyticsExport')->willReturn($state);
            $this->assertNotEquals(AnalyticsService::SUCCESS, $this->analyticsService->handleOrder($order));
        }
    }

    /**
     * Test a credit memo for the full price of the order
     */
    public function testCreditMemoRefundFull() {
        $this->enableSettings(true,true,true);
        $creditMemo = $this->getMockedCreditMemo();
        $order = $this->getMockedOrderWithState(MagentoOrder::STATE_COMPLETE);

        // Validate we actually send
        $this->analyticsCallerMock->expects($this->once())->method('handleCreditMemo');

        //Order has been exported
        $order->expects($this->once())->method('getAwAnalyticsExport')->willReturn(AnalyticsService::EXPORT_STATUS_PROCESSED);
        //Order has exported for 100$
        $order->expects($this->atLeastOnce())->method('getAwAnalyticsAmount')->willReturn(100);
        // we are refunding 100
        $creditMemo->expects($this->atLeastOnce())->method('getGrandTotal')->willReturn(100);
        // So we expect 0 to be saved
        $order->expects($this->once())->method('setAwAnalyticsAmount')->with(0);
        $order->expects($this->once())->method('setAwAnalyticsExport')->with(AnalyticsService::EXPORT_STATUS_FULLY_REFUNDED);
        $order->expects($this->once())->method('save');
        $creditMemo->expects($this->any())->method('getOrder')->willReturn($order);

        $this->assertEquals(AnalyticsService::SUCCESS, $this->analyticsService->handleCreditmemo($creditMemo));
    }

    /**
     * Test partial refund
     */
    public function testCreditMemoRefundPartial() {
        $this->enableSettings(true,true,true);
        $creditMemo = $this->getMockedCreditMemo();
        $order = $this->getMockedOrderWithState(MagentoOrder::STATE_COMPLETE);

        // Validate we actually send
        $this->analyticsCallerMock->expects($this->once())->method('handleCreditMemo');

        //Order has been exported
        $order->expects($this->once())->method('getAwAnalyticsExport')->willReturn(AnalyticsService::EXPORT_STATUS_PROCESSED);
        //Order has exported for 100$
        $order->expects($this->atLeastOnce())->method('getAwAnalyticsAmount')->willReturn(100);
        // we are refunding 50
        $creditMemo->expects($this->atLeastOnce())->method('getGrandTotal')->willReturn(50);
        // So we expect 50 to be saved
        $order->expects($this->once())->method('setAwAnalyticsAmount')->with(50);
        $order->expects($this->once())->method('save');
        $creditMemo->expects($this->any())->method('getOrder')->willReturn($order);

        $this->assertEquals(AnalyticsService::SUCCESS, $this->analyticsService->handleCreditmemo($creditMemo));
    }

    /**
     * Don't export if we would export too much
     */
    public function testCreditMemoRefundTooMuch() {
        $this->enableSettings(true,true,true);
        $creditMemo = $this->getMockedCreditMemo();
        $order = $this->getMockedOrderWithState(MagentoOrder::STATE_COMPLETE);

        // We should NEVER refund too much
        $this->analyticsCallerMock->expects($this->never())->method('handleCreditMemo');

        //Order has been exported
        $order->expects($this->once())->method('getAwAnalyticsExport')->willReturn(AnalyticsService::EXPORT_STATUS_PROCESSED);
        //Order has exported for 100$
        $order->expects($this->atLeastOnce())->method('getAwAnalyticsAmount')->willReturn(50);
        // we are refunding 50
        $creditMemo->expects($this->atLeastOnce())->method('getGrandTotal')->willReturn(100);
        $order->expects($this->never())->method('setAwAnalyticsAmount');
        $creditMemo->expects($this->any())->method('getOrder')->willReturn($order);

        $this->assertEquals(AnalyticsService::ERROR_CREDITMEMO_NEGATIVE_BALANCE, $this->analyticsService->handleCreditmemo($creditMemo));
    }

    /**
     * Test with module disabled
     */
    public function testDisablingModule(){
        $this->enableSettings(false,true,true);
        $order = $this->getMockedOrderWithState(MagentoOrder::STATE_PROCESSING);
        $creditMemo = $this->getMockedCreditMemo();

        $this->assertEquals(AnalyticsService::ERROR_DISABLED, $this->analyticsService->handleOrder($order));
        $this->assertEquals(AnalyticsService::ERROR_DISABLED, $this->analyticsService->handleCreditmemo($creditMemo));
    }

    /**
     * Test if order hit is disabled
     */
    public function testDisablingOrderHit(){
        $this->enableSettings(true,false,true);
        $order = $this->getMockedOrderWithState(MagentoOrder::STATE_PROCESSING);

        $this->assertEquals(AnalyticsService::ERROR_DISABLED, $this->analyticsService->handleOrder($order));
    }

    /**
     * Test if credit memo hit is disabled
     */
    public function testDisablingCreditMemo(){
        $this->enableSettings(true,true,false);
        $creditMemo = $this->getMockedCreditMemo();

        $this->assertEquals(AnalyticsService::ERROR_DISABLED, $this->analyticsService->handleCreditmemo($creditMemo));
    }

    /**
     * Test if we can handle creditMemo if order hits are disabled
     */
    public function testCreditMemoWithDisabledOrderHits() {
        $this->enableSettings(true, false, true);
        $order = $this->getMockedOrderWithState(MagentoOrder::STATE_PROCESSING);

        $order->expects($this->once())->method('getGrandTotal')->willReturn(100);
        $order->expects($this->any())->method('setAwAnalyticsAmount')->withConsecutive([100], [0]);
        $order->expects($this->any())->method('setAwAnalyticsExport')->withConsecutive([AnalyticsService::EXPORT_STATUS_ORDER_HIT_DISABLED], [AnalyticsService::EXPORT_STATUS_FULLY_REFUNDED]);
        $order->expects($this->any())->method('save');


        $this->assertEquals(AnalyticsService::ERROR_DISABLED, $this->analyticsService->handleOrder($order));

        $order->expects($this->any())->method('getAwAnalyticsAmount')->willReturn(100);
        $order->expects($this->any())->method('getAwAnalyticsExport')->willReturn(AnalyticsService::EXPORT_STATUS_ORDER_HIT_DISABLED);

        // Try creating a creditMemo
        $creditMemo = $this->getMockedCreditMemo();
        $creditMemo->expects($this->any())->method('getOrder')->willReturn($order);
        $creditMemo->expects($this->any())->method('getGrandTotal')->willReturn(100);

        $this->assertEquals(AnalyticsService::SUCCESS, $this->analyticsService->handleCreditmemo($creditMemo));
    }

    /**
     * Get mocked order with specific state
     *
     * @param $state
     * @return MagentoOrder
     */
    private function getMockedOrderWithState($state){
        $order = $this->getMockBuilder(MagentoOrder::class)
            ->disableOriginalConstructor()
            ->addMethods(['getAwAnalyticsExport', 'setAwAnalyticsExport', 'getAwAnalyticsAmount', 'setAwAnalyticsAmount'])
            ->onlyMethods(['getState','addCommentToStatusHistory', 'save', 'getGrandTotal'])
            ->getMock();
        $order->expects($this->any())->method('getState')->willReturn($state);
        return $order;
    }

    /**
     * Get a credit memo mock
     * @return Creditmemo
     */
    private function getMockedCreditMemo() {
        $creditMemo = $this->getMockBuilder(Creditmemo::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $creditMemo;
    }
}
