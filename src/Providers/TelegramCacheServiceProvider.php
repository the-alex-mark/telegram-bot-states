<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Support\ServiceProvider;
use ProgLib\Telegram\Bot\Console\TelegramClearCommand;

class TelegramCacheServiceProvider extends ServiceProvider {

    /**
     * @inheritDoc
     */
    public function boot() {
        if ($this->app->runningInConsole()) {

            // Регистрация команд
            $this->commands([
                'telegram.bot.cache.command.clear'
            ]);
        }

        // Параметры буфера по умолчанию
        $default_name   = $this->app['config']->get('cache.default', '');
        $default_config = $this->app['config']->get("cache.channels.$default_name", []);

        // Параметры новых буферов
        $stores = $this->app['config']->get('telegram.cache.stores', []);
        $prefix = $this->app['config']->get('telegram.cache.prefix', []);

        // Создание буферов
        foreach ($stores as $name => $config) {
            if (empty($config))
                $config = $default_config;

            $this->app['config']->set("cache.stores.{$prefix}{$name}", $config);
        }
    }

    /**
     * @inheritDoc
     */
    public function register() {

        // Регистрация фасада для работы с буфером по умолчанию
        $this->app->singleton('telegram.bot.cache', function ($app) {
            $stores = $app['config']->get('cache.stores');
            $name   = $app['config']->get('telegram.cache.default');
            $prefix = $app['config']->get('telegram.cache.prefix');

            if (array_key_exists($prefix . $name, $stores))
                $name = $prefix . $name;

            return $app['cache']->store($name);
        });

        // Регистрация команды очистки буфера
        $this->app->singleton('telegram.bot.cache.command.clear', function ($app) {
            return new TelegramClearCommand($app['cache'], $app['files']);
        });
    }
}
