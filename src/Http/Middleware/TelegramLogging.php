<?php

namespace ProgLib\Telegram\Bot\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Update;

/**
 * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
 */
class TelegramLogging {

    #region Properties

    /**
     * @var string Имя канала
     */
    protected $channel = 'telegram_updates';

    #endregion

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
            $update = Update::make($request->all());

            // Отчёт о параметрах входящего обновления
            Log::channel($this->channel)->debug($update->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            Log::channel($this->channel)->debug(str_repeat('-', 100));
        }

        return $next($request);
    }
}
