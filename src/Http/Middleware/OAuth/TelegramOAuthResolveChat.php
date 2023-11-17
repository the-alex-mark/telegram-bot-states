<?php

namespace ProgLib\Telegram\Bot\Http\Middleware\OAuth;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use ProgLib\Telegram\Bot\Models\TelegramChat;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Chat;

/**
 * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
 */
class TelegramOAuthResolveChat {

    /**
     * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
     * Выполняет валидацию входящих параметров запроса.
     *
     * @param  Request $request Входящие параметры запроса.
     * @param  Closure $next    Метод контроллера.
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next) {

        // Указание чата (пользователя) как авторизованного
        $request->setUserResolver(function ($guard = null) use ($request) {
            $chat = tb_api()->getChat([
                'chat_id' => $request->get('id')
            ]);

            // Регистрация нового чата (при его отсутствии)
            return TelegramChat::register($chat);
        });

        return $next($request);
    }
}
