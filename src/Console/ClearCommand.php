<?php

namespace ProgLib\Telegram\Console;

use Exception;
use Illuminate\Cache\Console\ClearCommand as BaseClearCommand;
use ProgLib\Telegram\Contracts\CacheStore as TelegramStoreContract;
use ProgLib\Telegram\Exceptions\TelegramCacheException;
use RuntimeException;

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
    public function handle() {

        // Запуск события перед очисткой
        $this->laravel['events']->dispatch('telegram_cache:clearing', [ $this->argument('store'), $this->tags() ]);

        try {
            $store  = $this->cache()->getStore();
            $result = ($store instanceof TelegramStoreContract)
                ? $store->clear()
                : $store->flush();
        }
        catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        if (!$result)
            throw new RuntimeException('Не удалось очистить буфер. Убедитесь, что у вас есть соответствующие разрешения.', 403);

        // Запуск события после очистки
        $this->laravel['events']->dispatch('telegram_cache:cleared', [ $this->argument('store'), $this->tags() ]);

        // Информирование об успешном выполнении
        $this->info('Telegram cache cleared!');
    }

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
