<?php

use Illuminate\Support\Facades\Cache;
use ProgLib\Telegram\Models\TelegramOption;

if (!function_exists('telegram_option_get')) {

    /**
     * Возвращает значение настройки.
     *
     * @param  string $option Ключ.
     * @param  mixed $default Значение по умолчанию.
     * @return mixed|false
     */
    function telegram_option_get($option, $default = false) {
        if (is_scalar($option))
            $option = trim($option);

        if (empty($option))
            return false;

        $key  = 'telegram_option_' . $option;
        $data = Cache::remember($key, config('cache.ttl'), function () use ($option, $default) {
            $records = TelegramOption::query()
                ->where('name', $option)->limit(1)
                ->get();

            if ($records->isNotEmpty())
                return $records->first()->value;

            return $default;
        });

        return $data;
    }
}

if (!function_exists('telegram_option_add')) {

    /**
     * Задаёт значение настройки.
     *
     * @param  string $option Ключ.
     * @param  mixed $value Значение.
     * @return mixed
     */
    function telegram_option_add($option, $value) {

        /** @var TelegramOption $model */
        $model = TelegramOption::query()->updateOrCreate(
            [ 'name'  => $option ],
            [ 'value' => $value ]
        );

        // Кеширование значения
        Cache::put('telegram_option_' . $option, $value, config('cache.ttl'));

        return $model->value;
    }
}
