<?php

namespace ProgLib\Telegram\Bot\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ProgLib\Telegram\Bot\Facades\Log;

/**
 * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
 */
class TelegramBotLogging {

    /**
     * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
     * Выполняет логирование входящих параметров запроса.
     *
     * @param  Request $request Входящие параметры запроса.
     * @param  Closure $next    Метод контроллера.
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next) {
        if (config('telegram.options.debug', false)) {
            Log::channel('updates')->debug('Входящий запрос от "' . tb_remote_ip() . '":', $request->all());
            Log::channel('updates')->debug(str_repeat('-', 100));
        }

        return $next($request);
    }
}
