<?php

namespace ProgLib\Telegram\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use ProgLib\Telegram\Cache\DataBaseStore as TelegramDataBaseStore;
use ProgLib\Telegram\Cache\FileStore as TelegramFileStore;
use ProgLib\Telegram\Console\ClearCommand as TelegramCacheClearCommand;
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
     * Boot service provider.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot() {
        if ($this->app->runningInConsole())
            $this->commands([ 'telegram_cache.command.clear' ]);

        // Регистрация конфигурации хранилища буфера в базе данных
        $this->app->make('config')->set('cache.stores.telegram_database', [
            'driver' => 'telegram_database'
        ]);

        // Регистрация конфигурации хранилища буфера в файлах
        $this->app->make('config')->set('cache.stores.telegram_file', [
            'driver' => 'telegram_file',
            'path' => storage_path('framework/cache/telegram'),
        ]);

        // Регистрация хранилища буфера в базе данных
        $this->app['cache']->extend('telegram_database', function($app) {
            return $app['cache']->repository(new TelegramDataBaseStore(false));
        });

        // Регистрация хранилища буфера в файлах
        $this->app['cache']->extend('telegram_file', function($app) {
            $path       = $app['config']['cache.stores.telegram_file.path'];
            $permission = $app['config']['cache.stores.telegram_file.permission'];

            return $app['cache']->repository(new TelegramFileStore($app['files'], $path, $permission ?? null));
        });
    }

    /**
     * @inheritDoc
     */
    public function register() {

        // Слияние конфигурации
        $this->mergeConfigFrom($this->config_path('telegram.php'), 'telegram');

        // Регистрация фасада для работы с буфером
        $this->app->singleton('telegram_cache', function ($app) {
            return $app['cache']->store($app['config']['telegram.cache.driver'] ?? 'telegram_database');
        });

        // Регистрация команды очистки буфера
        $this->app->singleton('telegram_cache.command.clear', function ($app) {
            return new TelegramCacheClearCommand($app['cache'], $app['files']);
        });
    }
}
