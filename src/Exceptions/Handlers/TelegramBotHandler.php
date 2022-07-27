<?php

namespace ProgLib\Telegram\Bot\Exceptions\Handlers;

use Illuminate\Foundation\Exceptions\Handler as BaseHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Exceptions\TelegramUndefinedPropertyException;
use Throwable;

class TelegramBotHandler extends BaseHandler {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $dontReport = [];

    /**
     * @var string Имя канала
     */
    protected $channel = 'telegram_errors';

    #endregion

    #region Overrides

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
        $data['trace'] = collect($e->getTrace())
            ->map(function ($trace) { return Arr::except($trace, [ 'args' ]); })
            ->all();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function register() {

        // Обработка исключений в работе «Telegram SDK»
        $this
            ->renderable(function (Throwable $e, $request) {
                if ($request->route()->getName() == 'telegram.bot.webhook')
                    return $this->telegramJson($request, $e);

                return null;
            });

        // Обработка исключений в работе «Telegram SDK»
        $this
            ->reportable(function (TelegramSDKException $e) {
                Log::channel($this->channel)->error('Ошибка в работе сервиса:', $this->convertExceptionToArray($e));
                Log::channel($this->channel)->error(str_repeat('-', 100));
            })
            ->stop();

        // Обработка исключений в работе «Telegram SDK»
        $this
            ->reportable(function (TelegramUndefinedPropertyException $e) {
                Log::channel($this->channel)->error('Ошибка в работе сервиса:', $this->convertExceptionToArray($e));
                Log::channel($this->channel)->error(str_repeat('-', 100));
            })
            ->stop();
    }

    #endregion

    /**
     * Преобразует исключение в работе бота «Telegram» в ответ Json.
     *
     * @param  Request   $request Параметры запроса.
     * @param  Throwable $e       Исключение.
     * @return JsonResponse
     */
    protected function telegramJson(Request $request, Throwable $e) {
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
}
