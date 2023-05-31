<?php

namespace Adwise\Analytics\Service;

use Adwise\Analytics\Api\MeasurementProtocol\ClientIdProviderInterface;
use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Model\DataProviders\MP\TimestampProvider;
use Adwise\Analytics\Model\MP\CreditMemoDataProviders as CreditMemoDataProvidersMP;
use Adwise\Analytics\Model\MP\OrderDataProviders as OrderDataProvidersMP;
use Adwise\Analytics\Model\MP\UserPropertyDataProviders as UserPropertyDataProvidersMP;
use Adwise\Analytics\Model\CreditMemoDataProviders as CreditMemoDataProvidersUA;
use Adwise\Analytics\Model\OrderDataProviders as OrderDataProvidersUA;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

class AnalyticsCaller
{
    private OrderDataProvidersMP $orderDataProvidersMP;
    private CreditMemoDataProvidersMP $creditMemoDataProvidersMP;
    private OrderDataProvidersUA $orderDataProvidersUA;
    private CreditMemoDataProvidersUA $creditMemoDataProvidersUA;
    private Data $dataHelper;
    private LoggerInterface $logger;
    private TimestampProvider $timestampProvider;
    private ClientIdProviderInterface $clientIdProvider;
    private UserPropertyDataProvidersMP $userPropertyDataProvidersMP;
    private ClientFactory $clientFactory;

    public function __construct(
        Data $dataHelper,
        OrderDataProvidersMP $orderDataProvidersMP,
        CreditMemoDataProvidersMP $creditMemoDataProvidersMP,
        OrderDataProvidersUA $orderDataProvidersUA,
        CreditMemoDataProvidersUA $creditMemoDataProvidersUA,
        TimestampProvider $timestampProvider,
        ClientIdProviderInterface $clientIdProvider,
        UserPropertyDataProvidersMP $userPropertyDataProviders,
        LoggerInterface $logger,
        ClientFactory $clientFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->orderDataProvidersMP = $orderDataProvidersMP;
        $this->creditMemoDataProvidersMP = $creditMemoDataProvidersMP;
        $this->orderDataProvidersUA = $orderDataProvidersUA;
        $this->creditMemoDataProvidersUA = $creditMemoDataProvidersUA;
        $this->logger = $logger;
        $this->timestampProvider = $timestampProvider;
        $this->clientIdProvider = $clientIdProvider;
        $this->userPropertyDataProvidersMP = $userPropertyDataProviders;
        $this->clientFactory = $clientFactory;
//        $this->userIdProvider = $userIdProvider;
    }


    private function buildFullPayload(array $event, OrderInterface $order, string $createdAt)
    {
        return array_filter([
            'client_id' => $this->clientIdProvider->getClientId($order),
            'events' => [$event],
            'user_properties' => $this->userPropertyDataProvidersMP->getData($order)
        ]);
    }

    /**
     * @param $order
     * @throws Exception
     */
    public function handleOrder($order)
    {
        if ($this->dataHelper->getMPPurchaseEventEnabled()) {
            $payload = $this->orderDataProvidersMP->getData($order);
            $payload = $this->buildFullPayload($payload, $order, $order->getCreatedAt());
            if ($this->dataHelper->getIsDebugLoggingEnabled()) {
                $this->logger->info('[Adwise_Analytics] Order payload MP', $payload);
            }
            $this->submitToGoogleMP($payload);
        }

        if ($this->dataHelper->getUAPurchaseEventEnabled()) {
            $payload = $this->orderDataProvidersUA->getData($order);
            if ($this->dataHelper->getIsDebugLoggingEnabled()) {
                $this->logger->info('[Adwise_Analytics] Order payload UA', $payload);
            }
            $this->submitToGoogleUA($payload);
        }
    }

    /**
     * @param $creditMemo
     * @throws Exception
     */
    public function handleCreditMemo($creditMemo)
    {
        if($this->dataHelper->getMPRefundEventEnabled()) {
            $payload = $this->creditMemoDataProvidersMP->getData($creditMemo);
            $payload = $this->buildFullPayload($payload, $creditMemo->getOrder(), $creditMemo->getCreatedAt());

            if ($this->dataHelper->getIsDebugLoggingEnabled()) {
                $this->logger->info('[Adwise_Analytics] Creditmemo payload MP', $payload);
            }

            $this->submitToGoogleMP($payload);
        }
        if ($this->dataHelper->getUARefundEventEnabled()) {
            $payload = $this->creditMemoDataProvidersUA->getData($creditMemo);
            if ($this->dataHelper->getIsDebugLoggingEnabled()) {
                $this->logger->info('[Adwise_Analytics] Creditmemo payload UA', $payload);
            }
            $this->submitToGoogleUA($payload);
        }
    }

    /**
     * @param $payload
     * @return bool|string
     * @throws Exception
     */
    public function submitToGoogleMP($payload)
    {
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

        $this->logger->debug(
            'result from google on url: ' . $this->getMpUrl() . ' : ' . $result->getBody()->getContents());

        return $result;
    }

    public function submitToGoogleUA($payload)
    {
        $ch = curl_init($this->dataHelper->getUAEndpoint());
        curl_setopt($ch, CURLOPT_USERAGENT, $this->dataHelper->getUserAgent());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return $result;
    }

    private function getMpUrl()
    {
        return $this->dataHelper->getMPEndpoint() . '?measurement_id=' . $this->dataHelper->getMPMeasurementId() . '&api_secret=' . $this->dataHelper->getMPApiSecret();
    }

}
