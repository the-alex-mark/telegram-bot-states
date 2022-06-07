<?php

namespace ProgLib\Telegram\Bot\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Представляет чат.
 *
 * @property int $id Идентификатор чата
 * @property string $username Имя пользователя
 * @property string $type Тип чата
 * @property-read string $url Адрес диалога чата
 */
class TelegramChat extends Model {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'id',
        'username',
        'type',
        'extra'
    ];

    /**
     * @inheritDoc
     */
    protected $appends = [
        'url'
    ];

    /**
     * @inheritDoc
     */
    protected $casts = [
        'extra' => 'array'
    ];

    /**
     * @inheritDoc
     */
    public $timestamps = true;

    /**
     * @var bool Определяет, требуется ли форматировать JSON
     */
    protected $json_pretty_print = true;

    #endregion

    #region Mutators

    /**
     * Возвращает адрес диалога чата «Telegram».
     *
     * @return null|string
     */
    public function getUrlAttribute() {
        if (!empty($this->username))
            return config('telegram.endpoints.messenger') . '/' . $this->username;

        return null;
    }

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
        $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        if (isset($this->json_pretty_print) && $this->json_pretty_print === true)
            $options |= JSON_PRETTY_PRINT;

        return (empty($value))
            ? null
            : json_encode($value, $options);
    }

    #endregion
}
