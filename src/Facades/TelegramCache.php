<?php

namespace ProgLib\Telegram\Bot\Facades;

use Illuminate\Support\Facades\Cache;

class TelegramCache extends Cache {

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor() {
        return 'telegram.bot.cache';
    }
}
