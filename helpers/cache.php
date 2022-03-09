<?php

use ProgLib\Telegram\Exceptions\TelegramCacheException;

if (!function_exists('telegram_cache')) {

    /**
     * Возвращает или задаёт значение буфера.
     *
     * @param array|string|null $key Ключ.
     * @param mixed $default Значение по умолчанию.
     * @return mixed|Illuminate\Contracts\Cache\Repository
     * @throws TelegramCacheException
     */
    function telegram_cache($key = null, $default = null) {
        try {

            // Возврат экземпляра «TelegramCache»
            if (is_null($key))
                return app('telegram_cache');

            // Установка списка значений
            if (is_array($key))
                return app('telegram_cache')->set($key, null);

            // Возврат значения указанных настроек
            return app('telegram_cache')->get($key, $default);
        }
        catch (Throwable $e) {
            throw new TelegramCacheException('Не удалось получить экземпляр для работы с буфером «Telegram».');
        }
    }
}
