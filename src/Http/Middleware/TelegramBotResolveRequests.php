<?php

namespace ProgLib\Telegram\Bot\Http\Middleware;

use Illuminate\Support\Collection;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
 */
class TelegramBotResolveRequests {

    #region Helpers

    /**
     * Выполняет авторизацию API.
     *
     * @return Api
     */
    protected function resolveRequestApi(Request $request) {
        $token  = $request->route('token');
        $config = Collection::make(Telegram::getConfig('bots'));

        $bot = $config
            ->where('token', $token)->keys()
            ->first();

        return Telegram::bot($bot);
    }

    #endregion

    /**
     * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
     * Выполняет авторизацию пользователя мессенджера.
     *
     * @param  Request $request Входящие параметры запроса.
     * @param  Closure $next    Метод контроллера.
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next) {

        // Авторизация API
        $request->{'setTelegramResolver'}(function () use ($request) {
            return $this->resolveRequestApi($request);
        });

        return $next($request);
    }
}
