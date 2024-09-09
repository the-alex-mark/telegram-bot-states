<?php

namespace ProgLib\Telegram\Bot\Api;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use ProgLib\Telegram\Bot\Api\Concerns\HasLogging;
use Telegram\Bot\HttpClients\GuzzleHttpClient as BaseGuzzleHttpClient;
use Telegram\Bot\HttpClients\HttpClientInterface;

class CliHttpClient extends BaseGuzzleHttpClient implements HttpClientInterface {

    use HasLogging;

    /**
     * @inheritDoc
     */
    public function send($url, $method, array $headers = [], array $options = [], $isAsyncRequest = false) {

        try {
            $data    = json_encode($options[RequestOptions::BODY], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $headers = Collection::make($headers)
                ->mapWithKeys(function ($value, $key) { return [ $key => "--header '" . $key . ": $value'" ]; })
                ->implode(' ');

            // Отправка запроса
            $response = shell_exec("curl --http1.0 --request $method --location '$url' " . $headers . " --data '$data'");
            $response = new Response(200, [], $response ?: '');
        }
        finally {

            // Логирование запроса
            $this->log($url, $options, $response ?? null);
        }

        return $response;
    }
}
