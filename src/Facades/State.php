<?php

namespace ProgLib\Telegram\Bot\Facades;

use Illuminate\Support\Facades\Facade;
use ProgLib\Telegram\Bot\BotsStateManager;

/**
 * ...
 *
 * @mixin BotsStateManager
 */
class State extends Facade {

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor() {
        return 'telegram.bot.states';
    }
}
