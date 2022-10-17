<?php

namespace ProgLib\Telegram\Bot\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use ProgLib\Telegram\Bot\Facades\Cache;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\User;

class TelegramBotController extends BaseController {

    /*
    |--------------------------------------------------------------------------
    | Telegram Webhook Controller
    |--------------------------------------------------------------------------
    |
    | ...
    |
    */

    #region Helpers

    /**
     * Возвращает информацию о боте.
     *
     * @return User
     */
    private function me() {
        return Cache::remember('bot_info', 720, function () {
            return request()->{'telegram'}()->getMe();
        });
    }

    #endregion

    /**
     * Обрабатывает маршрут веб-перехватчика для приёма запросов бота.
     *
     * @param  Request $request Параметра запроса.
     * @param  string  $token   Токен доступа API.
     * @return JsonResponse
     */
    public function webhook(Request $request, $token = null) {
        $update = Update::make($request->all());

        // Обработка команд
        if ($this->me()->id !== $update->getMessage()->from->id)
            $request->{'telegram'}()->commandsHandler(true);

//        // Выполнение состояний
//        TelegramState::process($update);

        // Технический ответ
        return response()->json([ 'ok' => true, 'description' => '' ]);
    }
}
