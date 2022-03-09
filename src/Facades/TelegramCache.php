<?php

namespace ProgLib\Telegram\Facades;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Lock lock(string $name, int $seconds = 0, mixed $owner = null)
 * @method static Lock restoreLock(string $name, string $owner)
 * @method static Repository  store(string|null $name = null)
 * @method static Store getStore()
 * @method static bool add(string $key, $value, DateTimeInterface|DateInterval|int $ttl = null)
 * @method static bool flush()
 * @method static bool forever(string $key, $value)
 * @method static bool forget(string $key)
 * @method static bool has(string $key)
 * @method static bool missing(string $key)
 * @method static bool put(string $key, $value, DateTimeInterface|DateInterval|int $ttl = null)
 * @method static int|bool decrement(string $key, $value = 1)
 * @method static int|bool increment(string $key, $value = 1)
 * @method static mixed get(string $key, mixed $default = null)
 * @method static mixed pull(string $key, mixed $default = null)
 * @method static mixed remember(string $key, DateTimeInterface|DateInterval|int $ttl, Closure $callback)
 * @method static mixed rememberForever(string $key, Closure $callback)
 * @method static mixed sear(string $key, Closure $callback)
 *
 * @see \Illuminate\Cache\CacheManager
 * @see \Illuminate\Cache\Repository
 */
class TelegramCache extends Facade {

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor() {
        return 'telegram_cache';
    }
}
