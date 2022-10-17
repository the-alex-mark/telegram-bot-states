<?php

namespace ProgLib\Telegram\Bot;

use Telegram\Bot\BotsManager as BaseBotsManager;

class BotsManager extends BaseBotsManager {

    /**
     * @inheritDoc
     */
    public function getConfig($key, $default = null) {
        $value = data_get($this->config, $key, $default);

        // Возможность указания обработчика запросов API в строчном представлении
        if ($key == 'http_client_handler') {
            if (is_string($value) && class_exists($value))
                return new $value();
        }

        return $value;
    }
}
