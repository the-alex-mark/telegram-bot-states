<?php

namespace ProgLib\Telegram\Bot\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ProgLib\Telegram\Bot\Facades\TelegramCache as Cache;
use ProgLib\Telegram\Bot\Facades\TelegramState;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\User;

trait TelegramBotWebhook {

    #region Helpers

    /**
     * Возвращает информацию о боте.
     *
     * @return User
     */
    private function me() {
        return Cache::remember('bot_info', 720, function () {
            return Telegram::getMe();
        });
    }

    #endregion

    /**
     * Обрабатывает маршрут веб-перехватчика для приёма запросов бота.
     *
     * @param  Request $request Параметра запроса.
     * @param  string  $token   Токен доступа HTTP API.
     * @return JsonResponse
     */
    public function webhook(Request $request, $token = null) {
        $update = Update::make($request->all());

        // Обработка команд
        if ($this->me()->id !== $update->getMessage()->from->id)
            Telegram::commandsHandler(true);

        // Выполнение состояний
        TelegramState::process($update);

        // Технический ответ
        return response()->json([ 'ok' => true, 'description' => '123' ]);
    }
}
