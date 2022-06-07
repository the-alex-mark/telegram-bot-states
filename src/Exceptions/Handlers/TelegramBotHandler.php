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
     * Преобразует указанное исключение в строку Json.
     *
     * @param  Throwable $e       Исключение.
     * @param  int       $options Параметры Json.
     * @return string
     */
    protected function convertExceptionToJson(Throwable $e, $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) {
        $error = $this->convertExceptionToArray($e);
        $error = json_encode($error, $options);

        return $error;
    }

    /**
     * Преобразует исключение в работе бота «Telegram» в ответ Json.
     *
     * @param  Request   $request Параметры запроса.
     * @param  Throwable $e       Исключение.
     * @return JsonResponse
     */
    protected function telegramJson(Request $request, Throwable $e) {
        return response()->json([
            'ok'          => false,
            'description' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error'
        ]);
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
            Log::channel($this->prefix . 'error')->error($this->convertExceptionToJson($e));
        })->stop();
    }
}
