<?php

namespace ProgLib\Telegram\Bot\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use ProgLib\Telegram\Bot\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
 */
class TelegramBotValidation {

    /**
     * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
     * Выполняет валидацию входящих параметров запроса.
     *
     * @param  Request $request Входящие параметры запроса.
     * @param  Closure $next    Метод контроллера.
     * @return JsonResponse
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next) {

        // Верификация токена API
        if ($request->route('token') !== Telegram::bot()->getAccessToken())
            return response()->json([ 'ok' => false, 'description' => 'Forbidden' ]);

        // Валидация входящих параметров запроса
        $validator = Validator::make($request->all(), [
            'update_id'      => [ 'bail', 'required', 'numeric', 'integer', 'min:0' ],
            'message'        => [ 'bail', 'nullable', 'filled', 'array' ],
            'callback_query' => [ 'bail', 'nullable', 'filled', 'array' ],
            'inline_query'   => [ 'bail', 'nullable', 'filled', 'array' ]
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);

        return $next($request);
    }
}
