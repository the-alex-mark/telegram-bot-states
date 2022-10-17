<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class TelegramLogServiceProvider extends ServiceProvider {

    #region Helpers

    /**
     * Возвращает расположение файлов конфигурации относительно модуля.
     *
     * @param  string $value
     * @return string
     */
    private function config_path($value = '') {
        if (!empty($value) && !Str::startsWith('\\', $value) && !Str::startsWith('/', $value))
            $value = DIRECTORY_SEPARATOR . $value;

        return implode(DIRECTORY_SEPARATOR, array( __DIR__, '..', '..', 'config' )) . $value;
    }

    #endregion

    /**
     * @inheritDoc
     */
    public function provides() {
        return [
            'telegram.bot.config.logging'
        ];
    }

    /**
     * @inheritDoc
     */
    public function boot() {
        if ($this->app->runningInConsole()) {

            // Публикация конфигурации журнала
            $this->publishes([
                $this->config_path('logging.php') => config_path('telegram.logging.php')
            ], 'telegram.bot.configurations.logging');
        }

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

        // Слияние конфигурации
        $this->mergeConfigFrom($this->config_path('logging.php'), 'telegram.logging');

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
