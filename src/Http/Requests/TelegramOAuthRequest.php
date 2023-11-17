<?php

namespace ProgLib\Telegram\Bot\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use ProgLib\Telegram\Bot\Models\TelegramChat;
use ProgLib\Telegram\Bot\Rules\TelegramOAuthDateRule;
use ProgLib\Telegram\Bot\Rules\TelegramOAuthHashRule;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Chat;

/**
 * Проверяет корректность предоставленных авторизационных данных.
 */
class TelegramOAuthRequest extends FormRequest {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $stopOnFirstFailure = true;

    #endregion

    /**
     * @inheritDoc
     */
    protected function prepareForValidation() {
        $this->merge([
            'bot_name' => $this->route()->parameter('bot_name')
        ]);
    }

    /**
     * @inheritDoc
     */
    public function rules() {
        return [
            'id' => [ 'bail', 'required', 'numeric', 'min:0' ],
            'auth_date' => [ 'bail', 'required', 'numeric', new TelegramOAuthDateRule() ],
            'hash' => [ 'bail', 'required', 'string', new TelegramOAuthHashRule() ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function messages() {
        return [
            '*' => trans('telegram::validation.oauth.all')
        ];
    }
}
