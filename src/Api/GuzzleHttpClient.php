<?php

namespace ProgLib\Telegram\Bot\Api;

use GuzzleHttp\TransferStats;
use ProgLib\Telegram\Bot\Api\Concerns\HasLogging;
use Telegram\Bot\HttpClients\GuzzleHttpClient as BaseGuzzleHttpClient;
use Telegram\Bot\HttpClients\HttpClientInterface;

class GuzzleHttpClient extends BaseGuzzleHttpClient implements HttpClientInterface {

    use HasLogging;

    /**
     * @inheritDoc
     */
    public function send($url, $method, array $headers = [], array $options = [], $isAsyncRequest = false) {
        $options['on_stats'] = function (TransferStats $stats) use ($options) {
            $this->log($stats->getEffectiveUri(), $options, $stats->getResponse());
        };

        return parent::send($url, $method, $headers, $options, $isAsyncRequest);
    }
}
