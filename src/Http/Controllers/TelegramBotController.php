<?php

namespace ProgLib\Telegram\Bot\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Event;
use ProgLib\Telegram\Bot\Facades\Cache;
use ProgLib\Telegram\Bot\Http\Requests\TelegramOAuthRequest;
use Telegram\Bot\Api;
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
    protected function me() {

        /** @var Api $telegram */
        $telegram = request()->{'telegram'}();
        $token    = $telegram->getAccessToken();

        return Cache::remember("bot_info_$token", 720, function () use ($telegram) {
            return $telegram->getMe();
        });
    }

    #endregion

    /**
     * Обрабатывает маршрут авторизации пользователя через «Telegram».
     *
     * @param  TelegramOAuthRequest $request Параметра запроса.
     * @return JsonResponse|RedirectResponse
     */
    public function oauth(TelegramOAuthRequest $request) {
        $response = [ 'ok' => true, 'description' => '' ];

        // Выполнение пользовательских событий
        Event::dispatch('telegram_bot:oauth', [ $request ]);

        // Технический ответ
        if ($request->expectsJson())
            return response()->json($response);

        return redirect()
            ->back()
            ->with('response', $response);
    }

    /**
     * Обрабатывает маршрут веб-перехватчика для приёма запросов бота.
     *
     * @param  Request $request Параметра запроса.
     * @param  string $token Токен доступа бота.
     * @return JsonResponse
     */
    public function webhook(Request $request, $token = null) {
        $update = Update::make($request->all());

        // Выполнение пользовательских событий
        Event::dispatch('telegram_bot:update', [ $update ]);

        // Обработка команд
        if ($this->me()->id !== $update->getMessage()->from->id)
            $request->{'telegram'}()->commandsHandler(true);

//        // Обработка сценариев состояний
//        State::process($update);

        // Технический ответ
        return response()->json([ 'ok' => true, 'description' => '' ]);
    }
}
