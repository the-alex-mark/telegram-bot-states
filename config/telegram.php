<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Common
    |--------------------------------------------------------------------------
    |
    | Общие параметры мессенджера.
    |
    */

    'url' => [
        'site' => env('TELEGRAM_URL_SITE', 'https://telegram.org'),
        'messenger' => env('TELEGRAM_URL_MESSENGER', 'https://t.me'),
        'api' => env('TELEGRAM_URL_API', 'https://api.telegram.org')
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Cache
    |--------------------------------------------------------------------------
    |
    | Настройки буфера.
    |
    */

    'cache' => [
        'driver' => env('TELEGRAM_CACHE_DRIVER', 'telegram_database')
    ]
];
