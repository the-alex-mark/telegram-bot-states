<?php

namespace ProgLib\Telegram\Bot\Logging;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Log\ParsesLogConfiguration as ParsesLogConfigurationBase;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger as Monolog;

trait ParsesLogConfiguration {

    use ParsesLogConfigurationBase;

    #region Parameters

    /**
     * The standard date format to use when writing logs.
     *
     * @var string
     */
    protected $dateFormat = 'Y.m.d H:i:s';

    #endregion

    /**
     * @inheritDoc
     */
    protected function getFallbackChannelName() {
        return app()->bound('env') ? app()->environment() : 'production';
    }

    /**
     * Объединяет два массива, так что параметры первого массива (передаваемые) заменяют при совпадении параметры второго массива (по умолчанию).
     * <br>
     * Параметры можно указать строкой.
     *
     * @param  array|string $args
     * @param  array        $defaults
     * @return array
     */
    protected function parseConfig($args, $defaults = array()) {
        if (is_object($args)) {
            $parsed_args = get_object_vars($args);
        } elseif (is_array($args)) {
            $parsed_args =& $args;
        } else {
            parse_str($args, $parsed_args);
        }

        if (is_array($defaults) && $defaults)
            return array_merge($defaults, $parsed_args);

        return $parsed_args;
    }

    /**
     * Prepare the handler for usage by Monolog.
     *
     * @param  HandlerInterface $handler
     * @param  array $config
     * @return HandlerInterface
     * @throws BindingResolutionException
     */
    protected function prepareHandler(HandlerInterface $handler, array $config = []) {
        if (Monolog::API !== 1 && (Monolog::API !== 2 || ! $handler instanceof FormattableHandlerInterface))
            return $handler;

        // Указание драйвера форматирования текста записи
        if (!isset($config['formatter'])) {
            $handler->setFormatter(tap(new LineFormatter(null, $this->dateFormat, true, true), function ($formatter) {
                $formatter->includeStacktraces();
            }));
        }
        elseif ($config['formatter'] !== 'default')
            $handler->setFormatter(app()->make($config['formatter'], $config['formatter_with'] ?? []));

//        // Инициализация процессоров
//        if (!empty($config['processors']) && is_array($config['processors'])) {
//            array_walk($config['processors'], function ($item) use ($handler) {
//                if (!empty($item))
//                    $handler->pushProcessor(is_callable($processor = $item) ? $processor : app()->make($processor));
//            });
//        }

        return $handler;
    }
}
