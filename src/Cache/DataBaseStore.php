<?php

namespace ProgLib\Telegram\Cache;

use Illuminate\Cache\RetrievesMultipleKeys;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\InteractsWithTime;
use ProgLib\Telegram\Contracts\CacheStore as CacheStoreContract;
use ProgLib\Telegram\Exceptions\TelegramAuthenticationException;
use ProgLib\Telegram\Exceptions\TelegramCacheException;
use ProgLib\Telegram\Models\TelegramChat;

class DataBaseStore extends TaggableStore implements LockProvider, CacheStoreContract {

    use InteractsWithTime;
    use RetrievesMultipleKeys;

    /**
     * Create a new Array store.
     *
     * @param  bool  $serializesValues
     * @return void
     */
    public function __construct($serializesValues = false) {
        $this->serializesValues = $serializesValues;
    }

    #region Properties

    /**
     * The array of stored values.
     *
     * @var array
     */
    protected $storage = [];

    /**
     * The array of locks.
     *
     * @var array
     */
    public $locks = [];

    /**
     * Indicates if values are serialized within the store.
     *
     * @var bool
     */
    protected $serializesValues;

    #endregion

    #region Helpers

    /**
     * Возвращает текущего авторизованного пользователя.
     *
     * @return TelegramChat
     * @throws TelegramAuthenticationException
     * @throws TelegramCacheException
     */
    protected function getChat() {
        if (is_null(request()->user()))
            throw new TelegramAuthenticationException('Попытка доступа к буферу для неавторизованного пользователя.', 401);

        if (!Schema::hasTable('telegram_chats') || !Schema::hasColumn('telegram_chats', 'chat_cache'))
            throw new TelegramCacheException('Для работы кеширования необходимо наличие столбца "chat_cache" в таблице "telegram_chats".', 401);

        return request()->user();
    }

    /**
     * Загружает буфер из хранилища.
     *
     * @return void
     * @throws TelegramAuthenticationException
     * @throws TelegramCacheException
     */
    protected function loadStorage() {
        $this->storage = $this->getChat()->chat_cache;
    }

    /**
     * Выгружает буфер в хранилище.
     *
     * @return bool
     * @throws TelegramAuthenticationException
     * @throws TelegramCacheException
     */
    protected function saveStorage() {
        return $this->getChat()->fill([ 'chat_cache' => $this->storage ])->save();
    }

    #endregion

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function get($key)
    {
        $this->loadStorage();

        if (! isset($this->storage[$key])) {
            return;
        }

        $item = $this->storage[$key];

        $expiresAt = $item['expiresAt'] ?? 0;

        if ($expiresAt !== 0 && $this->currentTime() > $expiresAt) {
            $this->forget($key);

            return;
        }

        return $this->serializesValues ? unserialize($item['value']) : $item['value'];
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function put($key, $value, $seconds)
    {
        $this->loadStorage();

        $this->storage[$key] = [
            'value' => $this->serializesValues ? serialize($value) : $value,
            'expiresAt' => $this->calculateExpiration($seconds),
        ];

        return $this->saveStorage();
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int
     */
    public function increment($key, $value = 1)
    {
        $this->loadStorage();

        if (! is_null($existing = $this->get($key))) {
            return tap(((int) $existing) + $value, function ($incremented) use ($key) {
                $value = $this->serializesValues ? serialize($incremented) : $incremented;

                $this->storage[$key]['value'] = $value;

                $this->saveStorage();
            });
        }

        $this->forever($key, $value);

        return $value;
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int
     */
    public function decrement($key, $value = 1)
    {
        return $this->increment($key, $value * -1);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        $this->loadStorage();

//        dd($this->storage);

        if (array_key_exists($key, $this->storage)) {
            unset($this->storage[$key]);

            return $this->saveStorage();
        }

        return false;
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return '';
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        $this->loadStorage();

        $this->storage = [];

        return $this->saveStorage();
    }

    /**
     * Get the expiration time of the key.
     *
     * @param  int  $seconds
     * @return int
     */
    protected function calculateExpiration($seconds)
    {
        return $this->toTimestamp($seconds);
    }

    /**
     * Get the UNIX timestamp for the given number of seconds.
     *
     * @param  int  $seconds
     * @return int
     */
    protected function toTimestamp($seconds)
    {
        return $seconds > 0 ? $this->availableAt($seconds) : 0;
    }

    /**
     * Get a lock instance.
     *
     * @param  string  $name
     * @param  int  $seconds
     * @param  string|null  $owner
     * @return Lock
     */
    public function lock($name, $seconds = 0, $owner = null)
    {
        return new DataBaseLock($this, $name, $seconds, $owner);
    }

    /**
     * Restore a lock instance using the owner identifier.
     *
     * @param  string  $name
     * @param  string  $owner
     * @return Lock
     */
    public function restoreLock($name, $owner)
    {
        return $this->lock($name, 0, $owner);
    }

    /**
     * @inheritDoc
     */
    public function clear() {
        TelegramChat::query()->update([ 'chat_cache' => null ]);
        return true;
    }
}
