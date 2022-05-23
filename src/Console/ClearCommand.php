<?php

namespace ProgLib\Telegram\Console;

use Exception;
use Illuminate\Cache\Console\ClearCommand as BaseClearCommand;
use ProgLib\Telegram\Exceptions\TelegramCacheException;

class ClearCommand extends BaseClearCommand {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $name = 'telegram:clear-cache';

    /**
     * @inheritDoc
     */
    protected $description = 'Выполняет полную очистку буфера для бота «Telegram».';

    #endregion

    /**
     * @inheritDoc
     * @throws TelegramCacheException
     */
    protected function cache() {
        try {
            return empty($this->tags())
                ? telegram_cache()
                : telegram_cache()->tags($this->tags());
        }
        catch (Exception $e) {
            return telegram_cache();
        }
    }
}
