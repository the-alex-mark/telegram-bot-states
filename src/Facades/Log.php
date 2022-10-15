<?php

namespace ProgLib\Telegram\Bot\Facades;

use Illuminate\Support\Facades\Log as BaseLog;

class Log extends BaseLog {

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor() {
        return 'telegram.bot.log';
    }

    /**
     * @inheritDoc
     */
    public static function channel($channel = null) {
        if (static::$app->hasBeenBootstrapped()) {
            $channels = static::$app['config']->get('telegram.logging.channels');
            $prefix   = static::$app['config']->get('telegram.logging.prefix');

            if (!is_null($channel)) {
                if (array_key_exists($channel, $channels))
                    $channel = $prefix . $channel;

                return static::$app['log']->channel($channel);
            }

            return static::$app['telegram.bot.log'];
        }

        return null;
    }
}
