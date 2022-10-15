<?php

namespace ProgLib\Telegram\Bot\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramLoggerHandler extends AbstractProcessingHandler {

    #region Parameters

    /**
     * @var Api
     */
    private $telegram = null;

    /**
     * @var int
     */
    private $chat_id = null;

    #endregion

    /**
     * ...
     *
     * @param int|string $level
     * @param int $chat_id Идентификатор чата.
     * @param string $bot_name Имя бота отправителя.
     */
    public function __construct($chat_id, $bot_name = null, $level = Logger::DEBUG) {
        parent::__construct($level);

        // Указание необходимых параметров
        $this->telegram = Telegram::bot($bot_name);
        $this->chat_id  = $chat_id;
    }

    /**
     * @throws TelegramSDKException
     */
    protected function write(array $record): void {
        $this->telegram->sendMessage([
            'chat_id'    => $this->chat_id,
            'parse_mode' => 'HTML',
            'text'       => $record['formatted']
        ]);
    }
}
