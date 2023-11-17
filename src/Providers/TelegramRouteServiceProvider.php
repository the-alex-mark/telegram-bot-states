<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramBotResolveChat;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramBotLogging;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramBotOAuth;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramBotResolveRequests;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramBotThrottleRequests;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramBotValidation;
use ProgLib\Telegram\Bot\Routing\TelegramBotRequestMethods;
use ProgLib\Telegram\Bot\Routing\TelegramBotRouteMethods;
use ReflectionException;

class TelegramRouteServiceProvider extends ServiceProvider {

    #region Helpers

    /**
     * ...
     *
     * @param  Router $router
     * @return void
     */
    protected function registerGroupMiddleware($router, $group, $middlewares) {
        foreach ($middlewares as $name => $class) {
            $router->aliasMiddleware("$group.$name", $class);
            $router->pushMiddlewareToGroup($group, "$group.$name");
        }
    }

    #endregion

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function boot() {

        /** @var Router $router_instance */
        $router_instance = $this->app['router'];

        // Регистрация промежуточного ПО для «OAuth»
        $this->registerGroupMiddleware($router_instance, 'telegram.oauth', [
            'resolve_chat' => TelegramBotResolveChat::class,
            'resolve_api'  => TelegramBotResolveRequests::class
        ]);

        // Регистрация промежуточного ПО для веб-перехватчика
        $this->registerGroupMiddleware($router_instance, 'telegram.bot', [
            'resolve_chat' => TelegramBotResolveChat::class,
            'resolve_api'  => TelegramBotResolveRequests::class,
            'throttle'     => TelegramBotThrottleRequests::class,
            'logging'      => TelegramBotLogging::class
        ]);

        // Регистрация маршрутов для работы веб-перехватчика
        $router_instance
            ->mixin(new TelegramBotRouteMethods());

        /** @var Request $request_instance */
        $request_instance = $this->app['request'];

        // Регистрация методов авторизации API
        $request_instance
            ->mixin(new TelegramBotRequestMethods());
    }

    /**
     * @inheritDoc
     */
    public function map() {

        /** @var Router $router_instance */
        $router_instance = $this->app['router'];

        if (!$router_instance->has('telegram.bot.oauth')) {
            if ($router_instance->hasMacro('telegram_bot_oauth'))
                $router_instance->{'telegram_bot_oauth'}();
        }

        if (!$router_instance->has('telegram.bot.webhook')) {
            if ($router_instance->hasMacro('telegram_bot_webhook'))
                $router_instance->{'telegram_bot_webhook'}();
        }
    }
}
