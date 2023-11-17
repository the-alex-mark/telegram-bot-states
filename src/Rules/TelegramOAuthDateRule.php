<?php

namespace ProgLib\Telegram\Bot\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Проверяет корректность предоставленных авторизационных данных.
 */
class TelegramOAuthDateRule implements Rule {

    /**
     * @inheritDoc
     */
    public function passes($attribute, $value) {
        if ((time() - $value) > 86400)
            return false;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function message() {
        return trans('telegram::validation.oauth.date');
    }
}
