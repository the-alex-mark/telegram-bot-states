<?php

namespace ProgLib\Telegram\Bot\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use ProgLib\Telegram\Bot\Traits\TelegramBotWebhook;

class TelegramBotController extends BaseController {

    /*
    |--------------------------------------------------------------------------
    | Telegram Webhook Controller
    |--------------------------------------------------------------------------
    |
    | ...
    |
    */

    use TelegramBotWebhook;
}
