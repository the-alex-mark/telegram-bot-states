<?php

namespace ProgLib\Telegram\Bot\Facades;

use Illuminate\Support\Facades\Cache as BaseCache;

class Cache extends BaseCache {

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor() {
        return 'telegram.bot.cache';
    }

    /**
     * @inheritDoc
     */
    public static function store($name = null) {
        if (static::$app->hasBeenBootstrapped()) {
            $stores = static::$app['config']->get('telegram.cache.stores');
            $prefix = static::$app['config']->get('telegram.cache.prefix');

            if (!is_null($name)) {
                if (array_key_exists($name, $stores))
                    $name = $prefix . $name;

                return static::$app['cache']->store($name);
            }

            return static::$app['telegram.bot.cache'];
        }

        return null;
    }
}
