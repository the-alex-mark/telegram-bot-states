<?php

namespace ProgLib\Telegram\Bot\Api;

use GuzzleHttp\TransferStats;
use ProgLib\Telegram\Bot\Facades\Log;
use Telegram\Bot\HttpClients\GuzzleHttpClient as BaseGuzzleHttpClient;
use Telegram\Bot\HttpClients\HttpClientInterface;

class GuzzleHttpClient extends BaseGuzzleHttpClient implements HttpClientInterface {

    /**
     * @inheritDoc
     */
    public function send($url, $method, array $headers = [], array $options = [], $isAsyncRequest = false) {
        $options['on_stats'] = function (TransferStats $stats) use ($options) {
            $request = $options['multipart'] ?? $options['form_params'] ?? $options['body'] ?? [];

            // Отчёт о параметрах запроса
            Log::channel('api')->debug('Запрос на адрес "' . $stats->getEffectiveUri() . '":', $request);

            // Отчёт о параметрах ответа
            if ($stats->hasResponse())
                Log::channel('api')->debug('Ответ:', json_decode($stats->getResponse()->getBody(), true));

            Log::channel('api')->debug(str_repeat('-', 100));
        };

        return parent::send($url, $method, $headers, $options, $isAsyncRequest);
    }
}
