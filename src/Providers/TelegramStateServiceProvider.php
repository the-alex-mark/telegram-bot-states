<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ProgLib\Telegram\Bot\Api\GuzzleHttpClient as CustomGuzzleHttpClient;
use ProgLib\Telegram\Bot\Console\TelegramWebhookCommand;
use ProgLib\Telegram\Bot\Exceptions\Handlers\TelegramBotHandler;
use ProgLib\Telegram\Bot\Sdk\BotsManager as CustomBotsManager;
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
     * @inheritDoc
     *
     * @throws BindingResolutionException
     */
    public function boot() {
        if ($this->app->runningInConsole()) {

            // Регистрация команд
            $this->commands([
                'telegram.bot.states.command.webhook'
            ]);

            // Публикация миграций
            $this->publishes([
                $this->database_path('migrations') => database_path('migrations')
            ], 'telegram.bot.migrations');

            // Публикация файлов локализации
            $this->publishes([
                $this->resource_path('lang') => lang_path()
            ], 'telegram.bot.translations');
        }

        // Регистрация файлов локализации
        $this->loadTranslationsFrom($this->resource_path('lang'), 'telegram');

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

        // Переопределение сервиса управления ботами
        $this->app->extend(BaseBotsManager::class, static function (BaseBotsManager $manager, $app) {
            return (new CustomBotsManager($app['config']['telegram']))->setContainer($app);
        });

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

