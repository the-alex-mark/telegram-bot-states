<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ProgLib\Telegram\Bot\Api\GuzzleHttpClient;
use ProgLib\Telegram\Bot\Console\TelegramWebhookCommand;
use ProgLib\Telegram\Bot\Exceptions\Handlers\TelegramBotHandler;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramAuthenticate;
use ProgLib\Telegram\Bot\Http\Middleware\TelegramLogging;
use ProgLib\Telegram\Http\Middleware\TelegramThrottleRequests;
use ReflectionException;

class TelegramStatesServiceProvider extends ServiceProvider {

    #region Properties

    /**
     * @var array Список доступных каналов журнала
     */
    protected $channels = [
        'api',
        'updates',
        'errors'
    ];

    #endregion

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
     * Устанавливает параметров конфигурации буфера.
     *
     * @return void
     * @throws BindingResolutionException
     */
    private function setConfigurationCache() {

        /** @var Repository $config_instance */
        $config_instance = $this->app->make('config');

        // Получение параметров буфера
        $driver = $config_instance->get('telegram.cache.driver', 'database');
        $params = $config_instance->get("telegram.cache.stores.$driver", []);

        // Создание каналов
        $config_instance->set('cache.stores.telegram', $params);
    }

    /**
     * Устанавливает параметров конфигурации журнала.
     *
     * @return void
     * @throws BindingResolutionException
     */
    private function setConfigurationLogging() {

        /** @var Repository $config_instance */
        $config_instance = $this->app->make('config');

        // Получение параметров журнала
        $driver = $config_instance->get('telegram.logging.driver', 'file');
        $params = $config_instance->get("telegram.logging.channels.$driver", []);

        // Создание каналов
        foreach ($this->channels as $channel) {
            $config_instance->set("logging.channels.telegram_$channel", array_replace_recursive($params, [
                'name' => 'telegram',
                'path' => storage_path(implode(DIRECTORY_SEPARATOR, [ 'logs', 'telegram', $channel, "telegram-$channel.log" ])),
            ]));
        }
    }

    /**
     * Устанавливает параметры конфигурации бота.
     *
     * @return void
     * @throws BindingResolutionException
     */
    private function setConfigurationBot() {

        /** @var Repository $config_instance */
        $config_instance = $this->app->make('config');

        // Переопределение клиента HTTP по умолчанию
        if (empty($config_instance->get('telegram.http_client_handler')))
            $config_instance->set('telegram.http_client_handler', new GuzzleHttpClient());
    }

    /**
     * Устанавливает параметры маршрутизации бота.
     *
     * @return void
     * @throws BindingResolutionException
     */
    private function setRoutesBot() {

        /** @var Router $router_instance */
        $router_instance = $this->app->make('router');

        // Регистрация посредников
        $router_instance->aliasMiddleware('telegram.bot.auth', TelegramAuthenticate::class);
        $router_instance->aliasMiddleware('telegram.bot.throttle', TelegramThrottleRequests::class);
        $router_instance->aliasMiddleware('telegram.bot.logging', TelegramLogging::class);
    }

    /**
     * @inheritDoc
     * @throws BindingResolutionException
     */
    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->commands([ 'telegram.bot.states.command.webhook' ]);

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

        // Установка параметров конфигурации
        $this->setConfigurationCache();
        $this->setConfigurationLogging();
        $this->setConfigurationBot();
        $this->setRoutesBot();
    }

    /**
     * @inheritDoc
     */
    public function register() {

        // Слияние конфигурации
        $this->mergeConfigFrom($this->config_path('telegram.php'), 'telegram');

        // Регистрация пользовательского обработчика исключений
        $this->app->singleton(ExceptionHandler::class, TelegramBotHandler::class);

        // Регистрация команды настройки веб-перехватчика
        $this->app->singleton('telegram.bot.states.command.webhook', function ($app) {
            return new TelegramWebhookCommand($app['telegram']);
        });

        // Регистрация сервис-провайдера для работы буфера
        $this->app->registerDeferredProvider(TelegramCacheServiceProvider::class);
    }
}

