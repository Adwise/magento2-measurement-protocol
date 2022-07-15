<?php

namespace Adwise\Analytics\Service;

use Adwise\Analytics\Helper\Data;
use Adwise\Analytics\Model\CreditMemoDataProviders;
use Adwise\Analytics\Model\OrderDataProviders;
use Exception;
use Psr\Log\LoggerInterface;

class AnalyticsCaller
{
    private $orderDataProviders;
    private $creditMemoDataProviders;

    private $dataHelper;

    private $logger;

    public function __construct(
        Data $dataHelper,
        OrderDataProviders $orderDataProviders,
        CreditMemoDataProviders $creditMemoDataProviders,
        LoggerInterface $logger
    ) {
        $this->dataHelper = $dataHelper;
        $this->orderDataProviders = $orderDataProviders;
        $this->creditMemoDataProviders = $creditMemoDataProviders;
        $this->logger = $logger;
    }

    /**
     * @param $order
     * @throws Exception
     */
    public function handleOrder($order)
    {
        $payload = $this->orderDataProviders->getData($order);
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
        $ch = curl_init($this->dataHelper->getGaCollectUrl());
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
}
