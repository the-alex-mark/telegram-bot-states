<?php

namespace ProgLib\Telegram\Bot\Logging\Via;

use Illuminate\Contracts\Container\BindingResolutionException;
use Monolog\Logger as Monolog;
use ProgLib\Telegram\Bot\Logging\ParsesLogConfiguration;
use ProgLib\Telegram\Bot\Logging\TelegramLoggerHandler;

class CustomizeTelegramLogging {

    use ParsesLogConfiguration;

    /**
     * Customize the given logger instance.
     *
     * @param  array $config
     * @return Monolog
     * @throws BindingResolutionException
     */
    public function __invoke($config) {

        // Установка параметров по умолчанию
        $args = $this->parseConfig($config, [
            'level'      => 'debug',
            'bot_name'   => null,
            'chat_id'    => null,
            'processors' => []
        ]);

        $channel = $this->parseChannel($config);
        $handler = new TelegramLoggerHandler($args['chat_id'], $args['bot_name'], $this->level($args));

        $test = [];
        // Инициализация процессоров
        if (!empty($args['processors']) && is_array($args['processors'])) {
            array_walk($args['processors'], function ($item) use ($handler, &$test) {
                if (!empty($item))
                    $test[] = is_callable($processor = $item) ? $processor : app()->make($processor);
//                    $handler->pushProcessor(is_callable($processor = $item) ? $processor : app()->make($processor));
            });
        }

        // Инициализация обработчика
        return new Monolog($channel, [ $this->prepareHandler($handler, $args) ], $test);
    }
}
