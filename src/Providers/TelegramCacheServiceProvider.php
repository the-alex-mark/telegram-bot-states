<?php

namespace ProgLib\Telegram\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use ProgLib\Telegram\Console\TelegramClearCommand;
use ProgLib\Telegram\Providers\Helpers\Path;

class TelegramCacheServiceProvider extends ServiceProvider implements DeferrableProvider {

    use Path;

    /**
     * @inheritDoc
     */
    public function provides() {
        return [
            'telegram_cache',
            'telegram_cache.command.clear'
        ];
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot() {
        if ($this->app->runningInConsole())
            $this->commands([ 'telegram_cache.command.clear' ]);

        // Получение параметров буфера
        $driver = $this->app->make('config')->get('telegram.cache.driver', 'database');
        $params = $this->app->make('config')->get("telegram.cache.stores.$driver", []);

        // Сохранение в общую конфигурацию
        $this->app->make('config')->set('cache.stores.telegram', $params);
    }

    /**
     * @inheritDoc
     */
    public function register() {

        // Слияние конфигурации
        $this->mergeConfigFrom($this->config_path('telegram.php'), 'telegram');

        // Регистрация фасада для работы с буфером
        $this->app->singleton('telegram_cache', function ($app) {
            return $app['cache']->store('telegram');
        });

        // Регистрация команды очистки буфера
        $this->app->singleton('telegram_cache.command.clear', function ($app) {
            return new TelegramClearCommand($app['cache'], $app['files']);
        });
    }
}
