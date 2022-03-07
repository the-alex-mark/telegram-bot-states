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
        $update  = new Update($request->all());
        $message = $update->getMessage();

        // Проверка отправителя на то, является ли он ботом
        if ($message->has('from') && $message->from->isBot)
            return response()->json([ 'ok' => false, 'description' => '' ]);

        // Фиксация чата как активного пользователя
        $request->setUserResolver(function ($guard = null) use ($message) {
            $chat    = $message->chat;
            $records = TelegramChat::query()->where('chat_id', $chat->id)->get();

            if ($records->isEmpty()) {
                return TelegramChat::query()->updateOrCreate(
                    [ 'chat_id'   => $chat->id ],
                    [ 'chat_type' => $chat->type ]
                );
            }

            return $records->first();
        });

        return $next($request);
    }
}
