<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ProgLib\Telegram\Bot\Console\TelegramClearCommand;

class TelegramCacheServiceProvider extends ServiceProvider {

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
            'telegram.bot.config.cache',
            'telegram.bot.cache.command.clear'
        ];
    }

    /**
     * @inheritDoc
     */
    public function boot() {
        if ($this->app->runningInConsole()) {

            // Регистрация команд
            $this->commands([
                'telegram.bot.cache.command.clear'
            ]);

            // Публикация конфигурации буфера
            $this->publishes([
                $this->config_path('cache.php') => config_path('telegram.cache.php')
            ], 'telegram.bot.config.cache');
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

        // Слияние конфигурации
        $this->mergeConfigFrom($this->config_path('cache.php'), 'telegram.cache');

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
