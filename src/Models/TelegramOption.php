<?php

namespace ProgLib\Telegram\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Представляет опцию.
 *
 * @property int $id Идентификатор записи
 * @property string $name Имя
 * @property mixed $value Значение
 */
class TelegramOption extends Model {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'name',
        'value'
    ];

    /**
     * @inheritDoc
     */
    public $timestamps = false;

    #endregion

    #region Mutators

    /**
     * Форматирует имя опции перед сохранением в БД.
     *
     * @param  mixed $value Имя.
     * @return void
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = is_scalar($value) ? trim($value) : $value;
    }

    /**
     * Сериализует значение опции перед сохранением в БД.
     *
     * @param  mixed $value Значение.
     * @return void
     */
    public function setValueAttribute($value) {
        $this->attributes['value'] = maybe_serialize($value);
    }

    /**
     * Десериализует значение опции после извлечения из БД.
     *
     * @param  mixed $value Значение.
     * @return mixed
     */
    public function getValueAttribute($value) {
        return maybe_unserialize($value);
    }

    #endregion
}
