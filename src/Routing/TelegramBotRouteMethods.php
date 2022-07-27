<?php

namespace ProgLib\Telegram\Bot\Routing;

use Illuminate\Routing\Router;

/**
 * Представляет маршруты веб-перехватчика.
 *
 * @mixin Router
 */
class TelegramBotRouteMethods {

    /**
     * Регистрирует маршруты веб-перехватчика.
     *
     * @phpstan-param array $options Параметры маршрутизации.
     * @return callable
     */
    public function telegram_bot_webhook() {

        /**
         * Регистрирует маршруты веб-перехватчика.
         *
         * @param  array $options Параметры маршрутизации.
         * @return void
         */
        return function ($options = []) {
            $middlewares = [
                'telegram.bot.validate',
                'telegram.bot.auth',
                'telegram.bot.throttle',
                'telegram.bot.logging'
            ];

            $namespace = 'App\Http\Controllers\Bots';
            $namespace = class_exists($this->prependGroupNamespace('TelegramBotController'))
                ? null
                : $namespace;

            if (!is_null($namespace) && !class_exists($namespace . '\TelegramBotController'))
                $namespace = 'ProgLib\Telegram\Bot\Http\Controllers';

            $this
                ->namespace($namespace)
                ->middleware($middlewares)
                ->prefix('telegram')
                ->group(function () {

                    $this
                        ->post('webhook/{token}', 'TelegramBotController@webhook')
                        ->name('telegram.bot.webhook');
                });
        };
    }
}
