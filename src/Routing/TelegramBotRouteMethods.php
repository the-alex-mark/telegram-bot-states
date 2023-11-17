<?php

namespace ProgLib\Telegram\Bot\Routing;

use Closure;
use Illuminate\Routing\Router;
use ProgLib\Telegram\Bot\Http\Controllers\TelegramBotController;

/**
 * Представляет методы регистрации новых маршрутов.
 *
 * @mixin Router
 */
class TelegramBotRouteMethods {

    /**
     * Регистрирует маршруты авторизации пользователя.
     *
     * @return Closure
     */
    public function telegram_bot_oauth() {

        /**
         * Регистрирует маршруты авторизации пользователя.
         *
         * @return void
         */
        return function () {
            $this
                ->middleware([ 'telegram.bot.resolve' ])
                ->get('telegram/bot/oauth/{bot_name}', [ TelegramBotController::class, 'oauth' ])
                ->name('telegram.bot.oauth');
        };
    }

    /**
     * Регистрирует маршруты веб-перехватчика.
     *
     * @return Closure
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
