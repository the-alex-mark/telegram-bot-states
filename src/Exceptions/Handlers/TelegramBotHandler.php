<?php

namespace ProgLib\Telegram\Bot\Exceptions\Handlers;

use Illuminate\Foundation\Exceptions\Handler as BaseHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ProgLib\Telegram\Bot\Exceptions\TelegramBreakException;
use ProgLib\Telegram\Bot\Facades\Log;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Exceptions\TelegramUndefinedPropertyException;
use Throwable;

class TelegramBotHandler extends BaseHandler {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $dontReport = [
        TelegramBreakException::class
    ];

    #endregion

    #region Helpers

    /**
     * @inheritDoc
     */
    protected function convertExceptionToArray(Throwable $e) {
        $data = [
            'message' => $e->getMessage(),
            'instance' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode()
        ];

        // Ошибки валидации
        if ($e instanceof ValidationException)
            $data['errors'] = $e->errors();

        // Трассировка
        $data['trace'] = explode(PHP_EOL, $e->getTraceAsString());

        return $data;
    }

    /**
     * Преобразует исключение в работе бота «Telegram» в ответ Json.
     *
     * @param  Request   $request Параметры запроса.
     * @param  Throwable $e       Исключение.
     * @return JsonResponse
     */
    protected function telegramInvalidJson(Request $request, Throwable $e) {
        $response = [
            'ok' => false,
            'description' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error'
        ];

        if ($e instanceof ValidationException)
            $response['description'] = 'Bad Request';

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
        $this
            ->renderable(function (Throwable $e, Request $request) {
                if (!empty($request->route())) {
                    if ($request->route()->getName() == 'telegram.bot.webhook')
                        return $this->telegramInvalidJson($request, $e);

                    if ($request->route()->getName() == 'telegram.bot.oauth') {
                        if ($request->expectsJson())
                            return $this->telegramInvalidJson($request, $e);
                    }
                }

                return null;
            });

        // Отчёт об исключениях в работе «Telegram SDK»
        $this
            ->reportable(function (TelegramSDKException $e) {
                Log::channel('errors')->error('Ошибка в работе сервиса:', $this->convertExceptionToArray($e));
                Log::channel('errors')->error(str_repeat('-', 100));
            })
            ->stop();

        // Отчёт об исключениях в работе «Telegram SDK»
        $this
            ->reportable(function (TelegramUndefinedPropertyException $e) {
                Log::channel('errors')->error('Ошибка в работе сервиса:', $this->convertExceptionToArray($e));
                Log::channel('errors')->error(str_repeat('-', 100));
            })
            ->stop();
    }

//    /**
//     * @inheritDoc
//     */
//    protected function invalidJson($request, ValidationException $exception) {
//        return $this->telegramInvalidJson($request, $exception);
//    }
}
