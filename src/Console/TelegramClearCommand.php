<?php

namespace ProgLib\Telegram\Console;

use Exception;
use Illuminate\Cache\Console\ClearCommand as BaseClearCommand;
use ProgLib\Telegram\Exceptions\TelegramCacheException;
use RuntimeException;

class TelegramClearCommand extends BaseClearCommand {

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

        // Запуск события перед выполнением команды
        $this->laravel['events']->dispatch('telegram_cache:clearing', [
            $this->argument('store'), $this->tags()
        ]);

        // Очистка буфера
        $result = $this->cache()->flush();

        if (!$result)
            throw new RuntimeException('Не удалось очистить буфер. Убедитесь, что у вас есть соответствующие разрешения.', 403);

        // Запуск события после выполнения команды
        $this->laravel['events']->dispatch('telegram_cache:cleared', [
            $this->argument('store'), $this->tags()
        ]);

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
