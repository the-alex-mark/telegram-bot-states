<?php

namespace ProgLib\Telegram\Bot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use ProgLib\Telegram\Bot\Facades\Cache;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\User;

/**
 * Представляет чат.
 *
 * @property int $id Идентификатор чата
 * @property string $username Имя чата
 * @property string $type Тип чата
 * @property array $extra Дополнительная информация
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

    #endregion

    #region Mutators

    /**
     * Возвращает адрес диалога чата «Telegram».
     *
     * @return null|string
     */
    public function getUrlAttribute() {
        if (!empty($this->username))
            return config('telegram.endpoint.messenger') . '/' . $this->username;

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
        if (!empty($value))
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return null;
    }

    #endregion

    #region Helpers

    /**
     * Возвращает информацию о текущем боте.
     *
     * @return User
     */
    public static function getInfoBot() {

        /** @var Api $telegram */
        $telegram = request()->{'telegram'}();
        $token    = $telegram->getAccessToken();

        return Cache::remember("bot_info_$token", 720, function () use ($telegram) {
            return $telegram->getMe();
        });
    }

    /**
     * Возвращает информацию о текущем чате.
     *
     * @return Chat
     */
    public static function getInfoChat() {

        /** @var Api $telegram */
        $telegram = request()->{'telegram'}();
        $chat_id  = request()->user()->id;

        return Cache::remember("chat_info_$chat_id", 60, function () use ($telegram, $chat_id) {
            return $telegram->getChat([ 'chat_id' => $chat_id ]);
        });
    }

    #endregion

    /**
     * Выполняет регистрацию чата в системе.
     * Возвращает созданную, либо уже существующую модель.
     *
     * @param  Chat $chat Объект чата.
     * @return TelegramChat
     */
    public static function register(Chat $chat) {

        /** @var TelegramChat $user */
        $model = self::query()->firstOrCreate([
            'id'       => $chat->id
        ], [
            'username' => $chat->username,
            'type'     => $chat->type
        ]);

        // Дополнительные действия при регистрации
        if ($model->wasRecentlyCreated)
            Event::dispatch('telegram_bot:register', [ $model ]);

        return $user;
    }
}
