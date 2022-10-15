<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Support\ServiceProvider;

class TelegramLogServiceProvider extends ServiceProvider {

    /**
     * @inheritDoc
     */
    public function boot() {

        // Параметры канала по умолчанию
        $default_name   = $this->app['config']->get('logging.default', '');
        $default_config = $this->app['config']->get("logging.channels.$default_name", []);

        // Параметры новых каналов
        $channels = $this->app['config']->get('telegram.logging.channels', []);
        $prefix   = $this->app['config']->get('telegram.logging.prefix', []);

        // Создание каналов
        foreach ($channels as $name => $config) {
            if (empty($config))
                $config = $default_config;

            $this->app['config']->set("logging.channels.{$prefix}{$name}", $config);
        }
    }

    /**
     * @inheritDoc
     */
    public function register() {

        // Регистрация фасада для работы с каналом журнала по умолчанию
        $this->app->singleton('telegram.bot.log', function ($app) {
            $channels = $app['config']->get('logging.channels');
            $name     = $app['config']->get('telegram.logging.default');
            $prefix   = $app['config']->get('telegram.logging.prefix');

            if (array_key_exists($prefix . $name, $channels))
                $name = $prefix . $name;

            return $app['log']->channel($name);
        });
    }
}
