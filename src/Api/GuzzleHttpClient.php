<?php

namespace ProgLib\Telegram\Bot\Api;

use GuzzleHttp\TransferStats;
use ProgLib\Telegram\Bot\Facades\Log;
use Telegram\Bot\HttpClients\GuzzleHttpClient as BaseGuzzleHttpClient;
use Telegram\Bot\HttpClients\HttpClientInterface;

class GuzzleHttpClient extends BaseGuzzleHttpClient implements HttpClientInterface {

    #region Helpers

    /**
     * Возвращает тело запроса на основе указанных параметров.
     *
     * @param  array $options Параметры запроса.
     * @return array
     */
    private function getRequestBody($options = []) {
        $params = [
            'multipart',
            'form_params',
            'body'
        ];

        foreach ($params as $param) {
            if (!empty($options[$param]))
                return $options[$param];
        }

        return [];
    }

    #endregion

    /**
     * @inheritDoc
     */
    public function send($url, $method, array $headers = [], array $options = [], $isAsyncRequest = false) {
        $options['on_stats'] = function (TransferStats $stats) use ($options) {
            $request = $this->getRequestBody($options);

            // Скрытие токена в целях безопасности
            $uri = $stats->getEffectiveUri();
            $uri = preg_replace('@/bot.+/@', '/***/', $uri);

            // Отчёт о параметрах запроса
            Log::channel('api')->debug('Исходящий запрос на адрес "' . $uri . '"' . (!empty($request) ? ':' : ''), $request);

            // Отчёт о параметрах ответа
            if ($stats->hasResponse())
                Log::channel('api')->debug('Ответ:', json_decode($stats->getResponse()->getBody(), true));

            Log::channel('api')->debug(str_repeat('-', 100));
        };

        return parent::send($url, $method, $headers, $options, $isAsyncRequest);
    }
}
