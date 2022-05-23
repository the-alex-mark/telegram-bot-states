<?php

namespace ProgLib\Telegram\Models;

use Illuminate\Database\Eloquent\Model;
use ProgLib\Telegram\Database\Eloquent\Concerns\HasOverrides;

/**
 * Представляет чат.
 *
 * @property int $id Идентификатор чата
 * @property string $username Имя пользователя
 * @property string $type Тип чата
 * @property-read string $url Адрес диалога чата
 */
class TelegramChat extends Model {

    use HasOverrides;

    #region Properties

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'id',
        'username',
        'type'
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
    public $timestamps = true;

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
}
