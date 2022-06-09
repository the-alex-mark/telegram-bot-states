<?php

namespace ProgLib\Telegram\Bot\Exceptions\Handlers;

use Illuminate\Foundation\Exceptions\Handler as BaseHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Throwable;

class TelegramBotHandler extends BaseHandler {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $dontReport = [];

    /**
     * @var string Префикс каналов журнала.
     */
    protected $prefix = 'telegram_';

    #endregion

    #region Overrides

    /**
     * @inheritDoc
     */
    protected function convertExceptionToArray(Throwable $e) {
        return [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, [ 'args' ]);
            })->all(),
        ];
    }

    #endregion

    #region Helpers

    /**
     * Преобразует исключение в работе бота «Telegram» в ответ Json.
     *
     * @param  Request   $request Параметры запроса.
     * @param  Throwable $e       Исключение.
     * @return JsonResponse
     */
    protected function telegramJson(Request $request, Throwable $e) {
        $response = [
            'ok'          => false,
            'description' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error'
        ];

        // Конкретизация ошибки при активном режиме отладки
        if (config('app.debug'))
            $response['exception'] = $this->convertExceptionToArray($e);

        return response()->json($response);
    }

    #endregion

    /**
     * @inheritDoc
     */
    public function register() {

        // Обработка исключений в работе «Telegram SDK»
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('bot/telegram/*'))
                return $this->telegramJson($request, $e);
        });

        // Обработка исключений в работе «Telegram SDK»
        $this->reportable(function (TelegramSDKException $e) {
            Log::channel($this->prefix . 'errors')->error('Ошибка в работе сервиса:', $this->convertExceptionToArray($e));
            Log::channel($this->prefix . 'errors')->error(str_repeat('-', 100));
        })->stop();
    }
}
