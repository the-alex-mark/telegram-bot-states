<?php

namespace ProgLib\Telegram\Bot\Routing;

use Illuminate\Routing\Router;
use ProgLib\Telegram\Bot\Http\Controllers\TelegramBotController;

/**
 * Представляет маршруты веб-перехватчика.
 *
 * @mixin Router
 */
class TelegramBotRouteMethods {

    /**
     * Регистрирует маршруты веб-перехватчика.
     *
     * @return callable
     */
    public function telegram_bot_webhook() {

        /**
         * Регистрирует маршруты веб-перехватчика.
         *
         * @return void
         */
        return function () {
            $this
                ->middleware([ 'telegram.bot' ])
                ->post('telegram/bot/webhook/{token}', [ TelegramBotController::class, 'webhook' ])
                ->name('telegram.bot.webhook');
        };
    }
}
