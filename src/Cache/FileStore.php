<?php

namespace ProgLib\Telegram\Cache;

use Illuminate\Cache\FileStore as BaseFileStore;
use ProgLib\Telegram\Contracts\CacheStore as CacheStoreContract;
use ProgLib\Telegram\Exceptions\TelegramAuthenticationException;
use ProgLib\Telegram\Models\TelegramChat;

class FileStore extends BaseFileStore implements CacheStoreContract {

    #region Helpers

    /**
     * Возвращает текущего авторизованного пользователя.
     *
     * @return TelegramChat
     * @throws TelegramAuthenticationException
     */
    protected function getChat() {
        if (is_null(request()->user()))
            throw new TelegramAuthenticationException('Попытка доступа к буферу для неавторизованного пользователя.', 401);

        return request()->user();
    }

    #endregion

    /**
     * @inheritDoc
     * @throws TelegramAuthenticationException
     */
    public function flush() {
        if (!$this->files->isDirectory($this->getDirectory()))
            return false;

        foreach ($this->files->directories($this->getDirectory()) as $directory) {
            $deleted = $this->files->deleteDirectory($directory);

            if (!$deleted || $this->files->exists($directory))
                return false;
        }

        if ($this->files->exists($this->getDirectory()))
            $this->files->deleteDirectory($this->getDirectory());

        return true;
    }

    /**
     * @inheritDoc
     * @throws TelegramAuthenticationException
     */
    protected function path($key) {
        $parts = array_slice(str_split($hash = sha1($this->getPrefix() . $key), 2), 0, 2);
        $path  = $this->getDirectory() . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR . $hash;

        return $path;
    }

    /**
     * @inheritDoc
     * @throws TelegramAuthenticationException
     */
    public function getDirectory() {
        return $this->directory . DIRECTORY_SEPARATOR . $this->getChat()->chat_id;
    }

    /**
     * @inheritDoc
     * @throws TelegramAuthenticationException
     */
    public function getPrefix() {
        return 'telegram_cache_' . $this->getChat()->chat_id . '_';
    }

    /**
     * @inheritDoc
     */
    public function clear() {
        if (!$this->files->isDirectory($this->directory))
            return false;

        foreach ($this->files->directories($this->directory) as $directory) {
            $deleted = $this->files->deleteDirectory($directory);

            if (!$deleted || $this->files->exists($directory))
                return false;
        }

        return true;
    }
}
