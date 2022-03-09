<?php

namespace ProgLib\Telegram\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ProgLib\Telegram\Console\ClearCommand as CacheClearCommand;
use ProgLib\Telegram\Cache\DataBaseStore as TelegramDataBaseStore;
use ProgLib\Telegram\Cache\FileStore as TelegramFileStore;
use ReflectionException;

class TelegramStatesServiceProvider extends ServiceProvider implements DeferrableProvider {

    #region Properties

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    #endregion

    #region Helpers

    /**
     * Возвращает расположение файлов конфигурации относительно модуля.
     *
     * @param  string $value
     * @return string
     */
    private static function config_path($value = '') {
        if (!empty($value) && !Str::startsWith('\\', $value) && !Str::startsWith('/', $value))
            $value = DIRECTORY_SEPARATOR . $value;

        return implode(DIRECTORY_SEPARATOR, array( __DIR__, '..', '..', 'config' )) . $value;
    }

    /**
     * Возвращает расположение файлов конфигурации относительно модуля.
     *
     * @param  string $value
     * @return string
     */
    private static function database_path($value = '') {
        if (!empty($value) && !Str::startsWith('\\', $value) && !Str::startsWith('/', $value))
            $value = DIRECTORY_SEPARATOR . $value;

        return implode(DIRECTORY_SEPARATOR, array( __DIR__, '..', '..', 'database' )) . $value;
    }

    /**
     * Возвращает расположение файлов конфигурации относительно модуля.
     *
     * @param  string $value
     * @return string
     */
    private static function resource_path($value = '') {
        if (!empty($value) && !Str::startsWith('\\', $value) && !Str::startsWith('/', $value))
            $value = DIRECTORY_SEPARATOR . $value;

        return implode(DIRECTORY_SEPARATOR, array( __DIR__, '..', '..', 'resources' )) . $value;
    }

    #endregion

    /**
     * Get the services provided by the provider.
     *
     * @return array
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
     * @throws ReflectionException
     */
    public function boot() {

        // Регистрация файлов миграции
        $this->loadMigrationsFrom(self::database_path('migrations'));

        // Регистрация файлов локализации
        $this->loadTranslationsFrom(self::resource_path('lang'), 'telegram');

        // Регистрация конфигурации хранилища буфера в базе данных
        $this->app->make('config')->set("cache.stores.telegram_database", [
            'driver' => 'telegram_database'
        ]);

        // Регистрация конфигурации хранилища буфера в файлах
        $this->app->make('config')->set("cache.stores.telegram_file", [
            'driver' => 'telegram_file',
            'path' => storage_path('framework/cache/telegram'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        // Слияние конфигурации
        $this->mergeConfigFrom(self::config_path('telegram.php'),  'telegram');

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

        // Регистрация фасада для работы с буфером
        $this->app->singleton('telegram_cache', function ($app) {
            return $app['cache']->store($app['config']['telegram.cache.driver'] ?? 'telegram_database');
        });

        // Регистрация команды очистки буфера
        $this->commands('telegram_cache.command.clear');
        $this->app->singleton('telegram_cache.command.clear', function ($app) {
            return new CacheClearCommand($app['cache'], $app['files']);
        });
    }
}

