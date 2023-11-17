<?php

namespace ProgLib\Telegram\Bot\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use ProgLib\Telegram\Bot\Models\TelegramChat;
use ProgLib\Telegram\Bot\Rules\TelegramOAuthDateRule;
use ProgLib\Telegram\Bot\Rules\TelegramOAuthHashRule;
use Telegram\Bot\Objects\Chat;

/**
 * Проверяет корректность предоставленных авторизационных данных.
 */
class TelegramUpdateRequest extends FormRequest {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $stopOnFirstFailure = true;

    #endregion

    /**
     * @inheritDoc
     */
    protected function passedValidation() {
        // ...

    }

    /**
     * @inheritDoc
     */
    public function rules() {
        return [
            'update_id' => [ 'bail', 'required', 'numeric', 'min:0' ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function messages() {
        return [
            '*' => trans('telegram::validation.update.all')
        ];
    }
}
