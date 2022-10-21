<?php

namespace Adwise\Analytics\Service;

use Adwise\Analytics\Api\MeasurementProtocol\ClientIdProviderInterface;
use Adwise\Analytics\Api\UserIdProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Model\MP\CreditMemoDataProviders;
use Adwise\Analytics\Model\DataProviders\TimestampProvider;
use Adwise\Analytics\Model\MP\UserPropertyDataProviders;
use Adwise\Analytics\Model\MP\OrderDataProviders;
use Exception;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;


class AnalyticsCaller
{
    private OrderDataProviders $orderDataProviders;
    private CreditMemoDataProviders $creditMemoDataProviders;
    private Data $dataHelper;
    private LoggerInterface $logger;
    private TimestampProvider $timestampProvider;
    private ClientIdProviderInterface $clientIdProvider;
    private UserIdProviderInterface $userIdProvider;
    private UserPropertyDataProviders $userPropertyDataProviders;
    private ClientFactory $clientFactory;

    public function __construct(
        Data $dataHelper,
        OrderDataProviders $orderDataProviders,
        CreditMemoDataProviders $creditMemoDataProviders,
        TimestampProvider $timestampProvider,
        ClientIdProviderInterface $clientIdProvider,
        UserPropertyDataProviders $userPropertyDataProviders,
        LoggerInterface $logger,
        ClientFactory $clientFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->orderDataProviders = $orderDataProviders;
        $this->creditMemoDataProviders = $creditMemoDataProviders;
        $this->logger = $logger;
        $this->timestampProvider = $timestampProvider;
        $this->clientIdProvider = $clientIdProvider;
        $this->userPropertyDataProviders = $userPropertyDataProviders;
        $this->clientFactory = $clientFactory;
    }


    private function buildFullPayload(array $event, OrderInterface $order, string $createdAt)
    {
        $timestamp = $this->timestampProvider->parseCreatedAtForMP($createdAt);
        return array_filter([
            'client_id' => $this->clientIdProvider->getClientId($order),
            'user_id' => $this->userIdProvider->getUserId($order),
            'events' => [$event],
            'timestamp_micros' => $timestamp,
            'user_properties' => $this->userPropertyDataProviders->getData($order)
        ]);
    }

    /**
     * @param $order
     * @throws Exception
     */
    public function handleOrder($order)
    {
        $payload = $this->orderDataProviders->getData($order);
        $payload = $this->buildFullPayload($payload, $order, $order->getCreatedAt());
        if ($this->dataHelper->getIsDebugLoggingEnabled()) {
            $this->logger->info('[Adwise_Analytics] Order payload', $payload);
        }
        $this->submitToGoogle($payload);
    }

    /**
     * @param $creditMemo
     * @throws Exception
     */
    public function handleCreditMemo($creditMemo) {

        $payload = $this->creditMemoDataProviders->getData($creditMemo);
        $payload = $this->buildFullPayload($payload, $creditMemo->getOrder(), $creditMemo->getCreatedAt());

        if ($this->dataHelper->getIsDebugLoggingEnabled()) {
            $this->logger->info('[Adwise_Analytics] Creditmemo payload', $payload);
        }

        $this->submitToGoogle($payload);
    }

    /**
     * @param $payload
     * @return bool|string
     * @throws Exception
     */
    public function submitToGoogle($payload){
        /** @var Client $client */
        $client = $this->clientFactory->create();
        $result = $client->post(
            $this->getMpUrl(),
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($payload)
            ]
        );

        $this->logger->debug('result from google on url: ' .$this->getMpUrl(). ' : ' . $result->getBody()->getContents());

        return $result;
    }

    private function getMpUrl() {
        return $this->dataHelper->getMPEndpoint() . '?measurement_id=' . $this->dataHelper->getMPMeasurementId() . '&api_secret=' . $this->dataHelper->getMPApiSecret();
    }

}
