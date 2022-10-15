<?php

use ProgLib\Logging\Tap\CustomizeLineFormatter;
use ProgLib\Telegram\Bot\Logging\Formatter\CustomizeTelegramJsonFormatter;
use ProgLib\Telegram\Bot\Logging\Via\CustomizeTelegramLogging;

if (!function_exists('tb_cache_driver')) {

    /**
     * ...
     *
     * @param $name
     * @param $params
     *
     * @return array|null
     */
    function tb_cache_driver($name, $params = []) {
        $drivers = [

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
        ];

        if (array_key_exists($name, $drivers))
            return array_merge($drivers[$name], $params);

        return null;
    }
}

if (!function_exists('tb_log_driver')) {

    /**
     * ...
     *
     * @param $name
     * @param $params
     *
     * @return array|null
     */
    function tb_log_driver($name, $params = []) {
        $drivers = [

            'file' => [
                'name' => 'telegram',
                'driver' => 'daily',
                'tap' => [ CustomizeLineFormatter::class ],
                'level' => env('TELEGRAM_LOG_LEVEL', 'debug'),
                'path' => storage_path('logs/telegram_bot/telegram.log'),
                'permission' => null,
                'locking' => true,
                'days' => 21
            ],

            'telegram' => [
                'name' => 'telegram',
                'driver' => 'custom',
                'via' => CustomizeTelegramLogging::class,
                'formatter' => CustomizeTelegramJsonFormatter::class,
                'formatter_with' => [
                    'dateFormat' => 'Y.m.d H:i:s'
                ],
                'level' => env('TELEGRAM_LOG_LEVEL', 'debug'),
                'bot_name' => env('TELEGRAM_LOG_BOT_NAME'),
                'chat_id' => env('TELEGRAM_LOG_CHAT_ID')
            ]
        ];

        if (array_key_exists($name, $drivers))
            return array_merge($drivers[$name], $params);

        return null;
    }
}
