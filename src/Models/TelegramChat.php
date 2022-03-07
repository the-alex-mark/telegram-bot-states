<?php

namespace ProgLib\Telegram\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Представляет чат.
 *
 * @property int $id Идентификатор записи
 * @property int $chat_id Идентификатор чата
 * @property string $chat_type Тип чата
 * @property array $chat_data Пользовательская информация
 */
class TelegramChat extends Model {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'chat_id',
        'chat_type',
        'chat_data'
    ];

    protected $casts = [
        'chat_data' => 'array'
    ];

    /**
     * @inheritDoc
     */
    public $timestamps = false;

    #endregion

    #region Mutators

    /**
     * @inheritDoc
     */
    protected function asJson($value) {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    #endregion
}
