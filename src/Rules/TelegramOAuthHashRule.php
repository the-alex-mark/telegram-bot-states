<?php

namespace ProgLib\Telegram\Bot\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Проверяет корректность предоставленных авторизационных данных.
 */
class TelegramOAuthHashRule implements Rule, DataAwareRule {

    #region Properties

    /**
     * @var array Параметры запроса
     */
    protected $data = [];

    /**
     * @var array Имена параметров, необходимых для формирования хэша
     */
    protected $fields = [
        'id',
        'first_name',
        'last_name',
        'username',
        'photo_url',
        'auth_date'
    ];

    #endregion

    #region Getters

    /**
     * @inheritDoc
     */
    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    #endregion

    /**
     * @inheritDoc
     */
    public function passes($attribute, $value) {
        $token = request()->{'telegram'}()->getAccessToken();
        $data  = collect($this->data)
            ->reject(function ($value, $key) { return !in_array($key, $this->fields); })
            ->map(function ($value, $key) { return $key . '=' . $value; })
            ->sort()
            ->join(PHP_EOL);

        $secret_key = hash('sha256', $token, true);
        $hash = hash_hmac('sha256', $data, $secret_key);

        if (strcmp($hash, $value) !== 0)
            return false;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function message() {
        return trans('telegram::validation.oauth.hash');
    }
}
