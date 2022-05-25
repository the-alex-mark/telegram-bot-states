<?php

namespace ProgLib\Telegram\Http\Middleware;

use ProgLib\Telegram\Models\TelegramChat;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Telegram\Bot\Objects\Update;

/**
 * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
 */
class TelegramAuthenticate {

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
        $request->setUserResolver(function ($guard = null) use ($message) {
            $chat = $message->chat;

            /** @var TelegramChat $user */
            $user = TelegramChat::query()->firstOrCreate([
                'id'       => $chat->id
            ], [
                'username' => $chat->username,
                'type'     => $chat->type
            ]);

            return $user;
        });

        return $next($request);
    }
}
