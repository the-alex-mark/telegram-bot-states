<?php

namespace ProgLib\Telegram\Bot\Api\Concerns;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use ProgLib\Telegram\Bot\Facades\Log;

trait HasLogging {

    /**
     * Выполняет запись в журнал.
     *
     * @param  string $url URL.
     * @param  array $options Параметры запроса.
     * @param  Response $response Параметры ответа.
     * @return void
     */
    private function log(string $url, array $options, Response $response) {
        $data = $options[RequestOptions::BODY] ?? [];

        // Скрытие токена в целях безопасности
        $uri = preg_replace('@/bot.+/@', '/***/', $url);

        // Отчёт о параметрах запроса
        Log::channel('api')->debug('Исходящий запрос на адрес "' . $uri . '"' . (!empty($data) ? ':' : ''), $data);

        // Отчёт о параметрах ответа
        if (!empty($response))
            Log::channel('api')->debug('Ответ:', json_decode($response->getBody()->getContents(), true));

        Log::channel('api')->debug(str_repeat('-', 100));
    }
}
