<?php

use ProgLib\Logging\Tap\CustomizeLineFormatter;
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

    'http_client_handler' => new GuzzleHttpClient(),

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

    # Предопределённые параметры сообщения
    'message' => [
        'parse_mode' => env('TELEGRAM_BOT_PARSE_MODE', 'MarkdownV2'),
        'disable_web_page_preview' => env('TELEGRAM_BOT_DISABLE_PREVIEW', true)
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
    'endpoints' => [
        'site' => env('TELEGRAM_URL_SITE', 'https://telegram.org'),
        'messenger' => env('TELEGRAM_URL_MESSENGER', 'https://t.me'),
        'api' => env('TELEGRAM_URL_API', 'https://api.telegram.org'),
        'core' => env('TELEGRAM_URL_CORE', 'https://my.telegram.org')
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Cache
    |--------------------------------------------------------------------------
    |
    | Настройки буфера.
    | Драйвер по умолчанию будет доступен в конфигурации буфера (cache.php) с
    | ключом "telegram".
    |
    */

    'cache' => [

        # Драйвер буфера по умолчанию
        'driver' => env('TELEGRAM_CACHE_DRIVER', 'database'),

        # Список реализованных драйверов
        'stores' => [

            'database' => [
                'driver' => 'database',
                'connection' => null,
                'table' => 'telegram_cache',
                'prefix' => '',

                # Параметры блокировки
                'lock_connection' => null,
                'lock_table' => 'telegram_cache_locks',
                'lock_lottery' => [ 2, 100 ]
            ],

            'file' => [
                'driver' => 'file',
                'path' => storage_path('framework/cache/telegram'),
                'permission' => null
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Logging
    |--------------------------------------------------------------------------
    |
    | Параметры журнала.
    |
    | Примечание:
    |    Параметры "name" и "path" настраиваются в сервис-провайдере
    |    «TelegramStatesServiceProvider».
    |
    */

    'logging' => [

        # Драйвер журнала по умолчанию
        'driver' => env('TELEGRAM_LOG_DRIVER', 'file'),

        # Список реализованных драйверов
        'channels' => [

            'file' => [
                'driver'     => 'daily',
                'tap'        => [ CustomizeLineFormatter::class ],
                'level'      => env('TELEGRAM_LOG_LEVEL', 'debug'),
                'permission' => null,
                'locking'    => true,
                'days'       => 21
            ]
        ]
    ]
];
