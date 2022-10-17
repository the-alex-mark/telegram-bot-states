<?php

namespace ProgLib\Telegram\Bot\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Routing\Middleware\ThrottleRequests as BaseThrottleRequests;
use Illuminate\Support\Carbon;
use ProgLib\Telegram\Bot\Facades\Cache;
use RuntimeException;
use Telegram\Bot\Answers\Answerable;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

/**
 * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
 */
class TelegramBotThrottleRequests extends BaseThrottleRequests {

    use Answerable;

    #region Helpers

    /**
     * Возвращает форматированное время.
     *
     * @param  int $seconds Количество секунд.
     * @return string
     */
    protected function formattedRetryAfter($seconds) {
        $time    = (new Carbon("00:00:00"))->modify("+ $seconds seconds");
        $minutes = (int)$time->format('i');
        $seconds = (int)$time->format('s');

        return (($minutes > 0) ? $this->formattedMinutes($minutes) : '') . (($seconds > 0) ? (($minutes > 0) ? ' и ' : '') . $this->formattedSeconds($seconds) : '');
    }

    /**
     * Возвращает форматированные время в секундах.
     *
     * @param  int $minutes Количество минут.
     * @return string
     */
    protected function formattedMinutes($minutes) {
        return $minutes . ' ' . trans_choice('минуту|минуты|минут', $minutes);
    }

    /**
     * Возвращает форматированные время в минутах.
     *
     * @param  int $seconds Количество секунд.
     * @return string
     */
    protected function formattedSeconds($seconds) {
        return $seconds . ' ' . trans_choice('секунду|секунды|секунд', $seconds);
    }

    #endregion

    /**
     * @inheritDoc
     */
    protected function resolveRequestSignature($request) {
        $chat = $this->update->getChat();

        if ($chat->has('id'))
            return sha1($chat->get('id'));

        throw new RuntimeException('Невозможно сформировать подпись запроса. Маршрут недоступен.');
    }

    /**
     * Обрабатывает запросы веб-перехватчика мессенджера «<b>Telegram</b>».
     * Выполняет ограничение по количеству принимаемых запросов за указанное время.
     *
     * @inheritDoc
     */
    public function handle($request, Closure $next, $maxAttempts = 50, $decayMinutes = 1, $prefix = 'telegram') {
        if (config('telegram.options.throttle.enabled', false) !== true)
            return $next($request);

        $this->telegram = $request->{'telegram'}();
        $this->update   = Update::make($request->all());

        try { $key = $prefix . $this->resolveRequestSignature($request); }
        catch (Exception $e) {
            return response()->json([ 'ok' => false, 'description' => $e->getMessage() ]);
        }

        $maxAttempts = config('telegram.options.throttle.attempts', $maxAttempts);
        $decayMinutes = config('telegram.options.throttle.during', $decayMinutes);
        $responseCallback = function ($request, $headers) use ($key, $decayMinutes) {
            Cache::remember($key, 10, function () use ($key, $decayMinutes) {
                $retryAfter = $this->getTimeUntilNextRetry($key);
                $message = trans('telegram.messages.too_many_attempts', [
                    'decay' => $this->formattedRetryAfter($decayMinutes * 60),
                    'retry' => $this->formattedRetryAfter($retryAfter)
                ]);

                return $this->replyWithMessage([
                    'text' => $message
                ]);
            });

            return response()->json([ 'ok' => false, 'description' => 'Too Many Attempts' ]);
        };

        return $this->handleRequest($request, $next, [
            (object)[
                'key'              => $key,
                'maxAttempts'      => $this->resolveMaxAttempts($request, $maxAttempts),
                'decayMinutes'     => $decayMinutes,
                'responseCallback' => $responseCallback,
            ],
        ]);
    }
}
