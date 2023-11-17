<?php

use ProgLib\Telegram\Bot\Api\GuzzleHttpClient;

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram HTTP Client Handler
    |--------------------------------------------------------------------------
    |
    | Клиент HTTP.
    |
    */

    'http_client_handler' => GuzzleHttpClient::class,

    /*
    |--------------------------------------------------------------------------
    | Telegram Webhook
    |--------------------------------------------------------------------------
    |
    | Дополнительные параметры настройки веб-перехватчика.
    |
    */

    'webhook' => [
        'max_connections' => 10,
        'allowed_updates' => json_encode([ 'message', 'inline_query', 'callback_query' ])
    ],

    # Предопределённые параметры запроса
    'pre' => [

        # Метод: sendMessage
        'message' => [
            'parse_mode' => env('TELEGRAM_BOT_PARSE_MODE', 'MarkdownV2'),
            'disable_web_page_preview' => env('TELEGRAM_BOT_DISABLE_PREVIEW', true)
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Common
    |--------------------------------------------------------------------------
    |
    | Дополнительные параметры мессенджера.
    |
    */

    'options' => [

        # Отладка запросов к API
        'debug' => env('TELEGRAM_BOT_DEBUG', false),

        # Ограничение по количеству запросов на веб-перехватчик
        'throttle' => [
            'enabled' => env('TELEGRAM_BOT_THROTTLE', false),
            'attempts' => env('TELEGRAM_BOT_THROTTLE_ATTEMPTS'),
            'during' => env('TELEGRAM_BOT_THROTTLE_DURING')
        ]
    ],

    # Ссылки на ресурсы мессенджера
    'endpoint' => [
        'site' => env('TELEGRAM_URL_SITE', 'https://telegram.org'),
        'messenger' => env('TELEGRAM_URL_MESSENGER', 'https://t.me'),
        'api' => env('TELEGRAM_URL_API', 'https://api.telegram.org'),
        'core' => env('TELEGRAM_URL_CORE', 'https://my.telegram.org')
    ]
];
