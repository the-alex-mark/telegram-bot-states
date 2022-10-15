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
    | Параметры буфера.
    |
    */

    'cache' => [

        # Драйвер буфера по умолчанию
        'default' => env('TELEGRAM_CACHE_STORAGE', 'file'),

        # Префикс имени драйвера (необходим для уникальности среди общего списка)
        'prefix' => env('TELEGRAM_CACHE_PREFIX', 'tb_'),

        # Список доступных драйверов
        'stores' => [

            'database' => tb_cache_driver('database'),

            'file' => tb_cache_driver('file')
        ],

        # Продолжительность хранения данных в буфере (в секундах)
        'ttl' => env('TELEGRAM_CACHE_TTL', 60)
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Logging
    |--------------------------------------------------------------------------
    |
    | Параметры журнала.
    |
    */

    'logging' => [

        # Канал по умолчанию
        'default' => env('TELEGRAM_LOG_CHANNEL', 'actions'),

        # Префикс имени канала (необходим для уникальности среди общего списка)
        'prefix' => env('TELEGRAM_LOG_PREFIX', 'tb_'),

        # Список доступных каналов
        'channels' => [

            # Технический канал
            # Примечание: Содержит отчёты о запросах к API сервиса «Telegram»
            'api' => tb_log_driver('file', [
                'path' => storage_path('logs/telegram_bot/api/telegram.log')
            ]),

            # Технический канал
            # Примечание: Содержит отчёты об отладочной информации
            'debug' => tb_log_driver('file', [
                'path' => storage_path('logs/telegram_bot/debug/telegram.log')
            ]),

            # Технический канал
            # Примечание: Содержит отчёты об обновлениях в чат-боте
            'updates' => tb_log_driver('file', [
                'path' => storage_path('logs/telegram_bot/updates/telegram.log')
            ]),

            # Технический канал
            # Примечание: Содержит отчёты об ошибках, произошедших во время работы «Telegram Bot States»
            'errors' => tb_log_driver('file', [
                'path' => storage_path('logs/telegram_bot/errors/telegram.log')
            ]),

            # Пользовательский канал
            'actions_file' => tb_log_driver('file', [
                'path' => storage_path('logs/telegram_bot/actions/telegram.log')
            ]),

            # Пользовательский канал
            'actions_telegram' => tb_log_driver('telegram'),

            # Пользовательский канал
            'actions' => [
                'name' => 'telegram',
                'driver' => 'stack',
                'channels' => [
                    env('TELEGRAM_LOG_PREFIX', 'tb_') . 'actions_file',
                    env('TELEGRAM_LOG_PREFIX', 'tb_') . 'actions_telegram'
                ]
            ]
        ]
    ]
];
