<?php

namespace ProgLib\Telegram\Bot\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ProgLib\Telegram\Bot\Models\TelegramChat;
use Telegram\Bot\Objects\Update;

/**
 * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
 */
class TelegramBotResolveChat {

    /**
     * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
     * Выполняет авторизацию пользователя мессенджера.
     *
     * @param  Request $request Входящие параметры запроса.
     * @param  Closure $next    Метод контроллера.
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next) {
        $update  = Update::make($request->all());
        $message = $update->getMessage();

        // Проверка отправителя на то, является ли он ботом
        if ($update->has('message') && $message->from->isBot)
            return response()->json([ 'ok' => false, 'description' => '' ]);

        // Указание чата (пользователя) как авторизованного
        $request->setUserResolver(function ($guard = null) use ($update) {
            $chat = $update->getChat();

            // Регистрация нового чата (при его отсутствии)
            return TelegramChat::register($chat);
        });

        return $next($request);
    }
}
