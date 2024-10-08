<?php

namespace ProgLib\Telegram\Bot\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
 */
class TelegramBotResolveRequests {

    /**
     * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
     * Выполняет авторизацию пользователя мессенджера.
     *
     * @param  Request $request Входящие параметры запроса.
     * @param  Closure $next    Метод контроллера.
     * @return JsonResponse
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next) {

        // Верификация токена бота
        $token  = $request->route()->parameter('bot_name');
        $config = collect(Telegram::getConfig('bots'))->where('token', $token);

        if ($config->isEmpty())
            abort(403, 'Forbidden');

        // Получение текущего бота
        $bot = $config->keys()->first();

        // Авторизация API
        $request->{'setTelegramResolver'}(function () use ($bot) {
            return Telegram::bot($bot);
        });

        return $next($request);
    }
}
