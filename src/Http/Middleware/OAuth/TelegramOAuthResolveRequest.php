<?php

namespace ProgLib\Telegram\Bot\Http\Middleware\OAuth;

use Closure;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
 */
class TelegramOAuthResolveRequest {

    /**
     * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
     * Выполняет валидацию входящих параметров запроса.
     *
     * @param  Request $request Входящие параметры запроса.
     * @param  Closure $next    Метод контроллера.
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {

        // Верификация имени пользователя бота
        $token  = $request->route()->parameter('bot_name');
        $config = collect(Telegram::getConfig('bots'))->where('username', $token);

        if ($config->isEmpty())
            abort(403, 'Forbidden');

        // Авторизация API
        $bot = $config->keys()->first();
        $request->{'setTelegramResolver'}(function () use ($bot) {
            return Telegram::bot($bot);
        });

        return $next($request);
    }
}
