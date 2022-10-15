<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramBotAuthenticate;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramBotLogging;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramBotThrottleRequests;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramBotValidation;
use ProgLib\Telegram\Bot\Routing\TelegramBotRouteMethods;
use ReflectionException;

class TelegramRouteServiceProvider extends ServiceProvider {

    /**
     * @inheritDoc
     *
     * @throws BindingResolutionException
     * @throws ReflectionException
     */
    public function boot() {

        /** @var Router $router_instance */
        $router_instance = $this->app['router'];
        $group           = 'telegram.bot';

        // Регистрация промежуточного ПО
        $router_instance
            ->aliasMiddleware($group . '.validate', TelegramBotValidation::class)
            ->aliasMiddleware($group . '.auth', TelegramBotAuthenticate::class)
            ->aliasMiddleware($group . '.throttle', TelegramBotThrottleRequests::class)
            ->aliasMiddleware($group . '.logging', TelegramBotLogging::class);

        $router_instance
            ->pushMiddlewareToGroup($group, $group . '.validate')
            ->pushMiddlewareToGroup($group, $group . '.auth')
            ->pushMiddlewareToGroup($group, $group . '.throttle')
            ->pushMiddlewareToGroup($group, $group . '.logging');

        // Регистрация маршрутов для работы веб-перехватчика
        $router_instance
            ->mixin(new TelegramBotRouteMethods());
    }

    /**
     * @inheritDoc
     */
    public function map() {

        /** @var Router $router_instance */
        $router_instance = $this->app['router'];

        if (!$router_instance->has('telegram.bot.webhook')) {
            if ($router_instance->hasMacro('telegram_bot_webhook'))
                $router_instance->{'telegram_bot_webhook'}();
        }
    }
}
