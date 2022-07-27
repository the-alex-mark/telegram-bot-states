<?php

namespace ProgLib\Telegram\Bot\Api;

use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\HttpClients\GuzzleHttpClient as BaseGuzzleHttpClient;
use Telegram\Bot\HttpClients\HttpClientInterface;

class GuzzleHttpClient extends BaseGuzzleHttpClient implements HttpClientInterface {

    #region Properties

    /**
     * @var string Префикс каналов журнала.
     */
    protected $channel = 'telegram_api';

    #endregion

    /**
     * @inheritDoc
     */
    public function send($url, $method, array $headers = [], array $options = [], $isAsyncRequest = false) {
        $options['on_stats'] = function (TransferStats $stats) use ($options) {
            $request = $options['multipart'] ?? $options['form_params'] ?? $options['body'] ?? [];

            // Отчёт о параметрах запроса
            Log::channel($this->channel)->debug('Запрос на адрес "' . $stats->getEffectiveUri() . '":', $request);

            // Отчёт о параметрах ответа
            if ($stats->hasResponse())
                Log::channel($this->channel)->debug('Ответ:', json_decode($stats->getResponse()->getBody(), true));

            Log::channel($this->channel)->debug(str_repeat('-', 100));
        };

        return parent::send($url, $method, $headers, $options, $isAsyncRequest);
    }
}
