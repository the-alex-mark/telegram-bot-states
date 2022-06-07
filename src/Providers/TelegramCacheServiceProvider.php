<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ProgLib\Telegram\Bot\Console\TelegramClearCommand;

class TelegramCacheServiceProvider extends ServiceProvider implements DeferrableProvider {

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
            'telegram.bot.cache',
            'telegram.bot.cache.command.clear'
        ];
    }

    /**
     * @inheritDoc
     */
    public function boot() {
        if ($this->app->runningInConsole())
            $this->commands([ 'telegram.bot.cache.command.clear' ]);
    }

    /**
     * @inheritDoc
     */
    public function register() {

        // Регистрация фасада для работы с буфером
        $this->app->singleton('telegram.bot.cache', function ($app) {
            return $app['cache']->store('telegram');
        });

        // Регистрация команды очистки буфера
        $this->app->singleton('telegram.bot.cache.command.clear', function ($app) {
            return new TelegramClearCommand($app['cache'], $app['files']);
        });
    }
}
