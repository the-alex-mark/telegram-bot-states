<?php

namespace ProgLib\Telegram\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
     * Boot service provider.
     *
     * @return void
     */
    public function boot() {

        // Регистрация файлов миграции
        $this->loadMigrationsFrom(self::database_path('migrations'));

        // Регистрация файлов локализации
        $this->loadTranslationsFrom(self::resource_path('lang'), 'telegram');
    }
}

