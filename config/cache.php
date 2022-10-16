<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Cache
    |--------------------------------------------------------------------------
    |
    | Параметры буфера.
    |
    */

    # Драйвер буфера по умолчанию
    'default' => env('TELEGRAM_CACHE_STORE', 'file'),

    # Префикс имени драйвера (необходим для уникальности среди общего списка)
    'prefix' => env('TELEGRAM_CACHE_PREFIX', 'tb_'),

    # Список доступных драйверов
    'stores' => [

        'database' => tb_cache_driver('database'),

        'file' => tb_cache_driver('file')
    ],

    # Продолжительность хранения данных в буфере (в секундах)
    'ttl' => env('TELEGRAM_CACHE_TTL', 3600)
];
