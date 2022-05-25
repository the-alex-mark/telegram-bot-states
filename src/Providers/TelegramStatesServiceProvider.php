<?php

namespace ProgLib\Telegram\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use ProgLib\Telegram\Console\TelegramWebhookCommand;
use ProgLib\Telegram\Providers\Helpers\Path;

class TelegramStatesServiceProvider extends ServiceProvider {

    use Path;

    #region Properties

    /**
     * @var array Список доступных каналов журнала
     */
    protected $channels = [
        'debug',
        'updates',
        'errors'
    ];

    #endregion

    public function provides() {
        return [
            'telegram_states.migrations',
            'telegram_states.translations',
            'telegram_states.command.webhook'
        ];
    }

    /**
     * @inheritDoc
     * @throws BindingResolutionException
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

        // Получение параметров журнала
        $driver = $this->app->make('config')->get('telegram.logging.driver', 'file');
        $params = $this->app->make('config')->get("telegram.logging.channels.$driver", []);

        // Создание каналов
        foreach ($this->channels as $channel) {
            $this->app->make('config')->set("logging.channels.telegram_$channel", array_replace_recursive($params, [
                'name' => 'telegram',
                'path' => storage_path(implode(DIRECTORY_SEPARATOR, [ 'logs', 'telegram', $channel, "telegram-$channel.log" ])),
            ]));
        }
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
        $this->app->register(TelegramCacheServiceProvider::class);
    }
}

