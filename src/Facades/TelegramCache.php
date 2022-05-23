<?php

namespace ProgLib\Telegram\Facades;

use Illuminate\Support\Facades\Cache;

class TelegramCache extends Cache {

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor() {
        return 'telegram_cache';
    }
}
