<?php

namespace ProgLib\Telegram\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use ProgLib\Telegram\Providers\Helpers\Path;

class TelegramStatesServiceProvider extends ServiceProvider implements DeferrableProvider {

    use Path;

    /**
     * Boot service provider.
     *
     * @return void
     */
    public function boot() {
        if ($this->app->runningInConsole()) {

            // Публикация миграций
            $this->publishes([
                $this->database_path('migrations') => database_path('migrations')
            ], 'migrations');

            // Публикация файлов локализации
            $this->publishes([
                $this->resource_path('lang') => lang_path()
            ], 'translations');
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

        // Регистрация сервис-провайдера для работы буфера
        $this->app->registerDeferredProvider(TelegramCacheServiceProvider::class);
    }
}

