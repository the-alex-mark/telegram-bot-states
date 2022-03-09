<?php

namespace ProgLib\Telegram\Contracts;

interface CacheStore {

    /**
     * Выполняет полную очистку буфера.
     *
     * @return bool
     */
    public function clear();
}
