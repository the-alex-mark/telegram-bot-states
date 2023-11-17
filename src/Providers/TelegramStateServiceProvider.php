<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ProgLib\Telegram\Bot\Api\GuzzleHttpClient as CustomGuzzleHttpClient;
use ProgLib\Telegram\Bot\BotsManager as CustomBotsManager;
use ProgLib\Telegram\Bot\BotsStateManager;
use ProgLib\Telegram\Bot\Console\TelegramWebhookCommand;
use ProgLib\Telegram\Bot\Exceptions\Handlers\TelegramBotHandler;
use Telegram\Bot\BotsManager as BaseBotsManager;

class TelegramStateServiceProvider extends ServiceProvider {

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

    /**
     * Возвращает расположение файлов конфигурации относительно модуля.
     *
     * @param  string $value
     * @return string
     */
    private function database_path($value = '') {
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
    private function resource_path($value = '') {
        if (!empty($value) && !Str::startsWith('\\', $value) && !Str::startsWith('/', $value))
            $value = DIRECTORY_SEPARATOR . $value;

        return implode(DIRECTORY_SEPARATOR, array( __DIR__, '..', '..', 'resources' )) . $value;
    }

    #endregion

    /**
     * @inheritDoc
     */
    public function provides() {
        return [
            'telegram.bot.migrations',
            'telegram.bot.translations',
            'telegram.bot.states',
            'telegram.bot.states.command.webhook'
        ];
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot() {
        if ($this->app->runningInConsole()) {

            // Регистрация команд
            $this->commands([
                'telegram.bot.states.command.webhook'
            ]);

            // Публикация конфигурации виджетов
            $this->publishes([
                $this->config_path('widgets.php') => config_path('telegram.widgets.php')
            ], 'telegram.bot.configurations.widgets');

            // Публикация миграций
            $this->publishes([
                $this->database_path('migrations/states') => database_path('migrations')
            ], 'telegram.bot.migrations');

            // Публикация файлов локализации
            $this->publishes([
                $this->resource_path('locales') => lang_path('vendor/telegram')
            ], 'telegram.bot.translations');

            // Публикация шаблонов
            $this->publishes([
                $this->resource_path('views') => $this->app->resourcePath('views/vendor/telegram')
            ], 'telegram.bot.views');
        }

        // Регистрация файлов локализации
        $this->loadTranslationsFrom($this->resource_path('locales'), 'telegram');

        // Регистрация шаблонов
        $this->loadViewsFrom($this->resource_path('views'), 'telegram');

        // Переопределение клиента HTTP
        if (empty($this->app['config']->get('telegram.http_client_handler')))
            $this->app['config']->set('telegram.http_client_handler', CustomGuzzleHttpClient::class);
    }

    /**
     * @inheritDoc
     */
    public function register() {

        // Слияние конфигурации
        $this->mergeConfigFrom($this->config_path('telegram.php'), 'telegram');
        $this->mergeConfigFrom($this->config_path('widgets.php'), 'telegram.widgets');

        // Переопределение сервиса управления ботами
        $this->app->extend(BaseBotsManager::class, static function (BaseBotsManager $manager, $app) {
            return (new CustomBotsManager($app['config']['telegram']))->setContainer($app);
        });

//        // Регистрация пользовательского обработчика исключений
//        $this->app->singleton('telegram.bot.states', function ($app) {
//            return new BotsStateManager($app['config']['telegram']);
//        });

        // Регистрация пользовательского обработчика исключений
        $this->app->singleton(ExceptionHandler::class, TelegramBotHandler::class);

        // Регистрация команды настройки веб-перехватчика
        $this->app->singleton('telegram.bot.states.command.webhook', function ($app) {
            return new TelegramWebhookCommand($app['telegram']);
        });

        // Регистрация дополнительных сервис-провайдеров
        $this->app->register(TelegramRouteServiceProvider::class);
        $this->app->register(TelegramLogServiceProvider::class);
        $this->app->register(TelegramCacheServiceProvider::class);
    }
}

