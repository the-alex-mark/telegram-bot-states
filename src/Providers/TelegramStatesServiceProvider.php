<?php

namespace ProgLib\Telegram\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use ProgLib\Telegram\Console\TelegramWebhookCommand;
use ProgLib\Telegram\Providers\Helpers\Path;

class TelegramStatesServiceProvider extends ServiceProvider implements DeferrableProvider {

    use Path;

    public function provides() {
        return [
            'telegram_states.migrations',
            'telegram_states.translations',
            'telegram_states.command.webhook'
        ];
    }

    /**
     * @inheritDoc
     */
    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->commands([ 'telegram_states.command.webhook' ]);

            // Публикация миграций
            $this->publishes([
                $this->database_path('migrations') => database_path('migrations')
            ], 'telegram_states.migrations');

            // Публикация файлов локализации
            $this->publishes([
                $this->resource_path('lang') => lang_path()
            ], 'telegram_states.translations');
        }

        // Регистрация файлов миграции
        $this->loadMigrationsFrom($this->database_path('migrations'));

        // Регистрация файлов локализации
        $this->loadTranslationsFrom($this->resource_path('lang'), 'telegram');
    }

    /**
     * @inheritDoc
     */
    public function register() {

        // Слияние конфигурации
        $this->mergeConfigFrom($this->config_path('telegram.php'), 'telegram');

        // Регистрация команды настройки веб-перехватчика
        $this->app->singleton('telegram_states.command.webhook', function ($app) {
            return new TelegramWebhookCommand($app['telegram']);
        });

        // Регистрация сервис-провайдера для работы буфера
        $this->app->registerDeferredProvider(TelegramCacheServiceProvider::class);
    }
}

