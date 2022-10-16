<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Logging
    |--------------------------------------------------------------------------
    |
    | Параметры журнала.
    |
    */

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
];
