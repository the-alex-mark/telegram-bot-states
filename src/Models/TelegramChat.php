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
 * @property array $chat_cache Буфер
 */
class TelegramChat extends Model {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'chat_id',
        'chat_type',
        'chat_data',
        'chat_cache'
    ];

    /**
     * @inheritDoc
     */
    protected $hidden = [
        'chat_cache'
    ];

    /**
     * @inheritDoc
     */
    protected $casts = [
        'chat_data' => 'array',
        'chat_cache' => 'array'
    ];

    /**
     * @inheritDoc
     */
    public $timestamps = true;

    #endregion

    #region Overrides

    /**
     * @inheritDoc
     */
    public function fromJson($value, $asObject = false) {
        $data = json_decode($value, !$asObject);

        if (!$asObject)
            $data = is_null($data) ? [] : $data;

        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function asJson($value) {
        return (empty($value))
            ? null
            : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    #endregion
}
