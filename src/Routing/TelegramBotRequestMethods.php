<?php

namespace ProgLib\Telegram\Bot\Routing;

use Illuminate\Http\Request;

/**
 * Представляет методы регистрации новых методов «Request».
 *
 * @mixin Request
 */
class TelegramBotRequestMethods {

    use Resolvers\ApiRequestResolver;
    use Resolvers\ChatRequestResolver;
}
