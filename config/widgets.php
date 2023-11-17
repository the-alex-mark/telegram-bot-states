<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Widgets
    |--------------------------------------------------------------------------
    |
    | Параметры виджетов.
    |
    */

    'login' => [
        'src' => env('TELEGRAM_WIDGET_LOGIN_SRC', 'https://telegram.org/js/telegram-widget.js?21'),
        'bot_name' => env('TELEGRAM_WIDGET_LOGIN_BOT_NAME'),
    ]
];
