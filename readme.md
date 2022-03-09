# Telegram Bot States for Laravel

Реализует состояния бота для мессенджера «**Telegram**».

<br>

## Cache

Кеширование реализовано [на базе «Laravel»](https://laravel.com/docs/9.x/cache) с разделением для каждого пользователя «Telegram», таким образом, чтобы под одним пользователем было невозможно очистить данные другого.
<br>
<br>
Для работы функционала необходим авторизованный пользователь, при его отсутствии будет выдано соответствующее исключение.
Для автоматической авторизации реализован посредник `ProgLib\Telegram\Http\Middleware\TelegramAuthenticate`, после выполнения которого будет доступен к использованию метод `request()->user()`.
<br>
Данный метод возвращает экземпляр модели `ProgLib\Telegram\Models\TelegramChat`.
<br>
<br>
Для работы буфера реализовано два новых драйвера: `telegram_database` (используется по умолчанию) и `telegram_file`. При необходимости можно добавить пользовательский [стандартными средствами фреймворка](https://laravel.com/docs/9.x/cache#adding-custom-cache-drivers).

<br>

Использование фасада:
```php
use ProgLib\Telegram\Facades\TelegramCache;

// Сохранение
$result = TelegramCache::put('key', 'value', config('cache.ttl'));

// Получение
$value  = TelegramCache::get('key', 'default');

// Удаление
$result = TelegramCache::forget('key');
```

<br>

Использование вспомогательного метода:
```php

// Сохранение
$result = telegram_cache()->put('key', 'value', config('cache.ttl'));
$result = telegram_cache([
    'key_1' => 'value',
    'key_2' => 'value'
]);

// Получение
$value  = telegram_cache('key', 'default');

// Удаление
$result = telegram_cache()->forget('key');
```

<br>

В случае переполнения хранилища буфера существует команда для полной её очистки независимо от авторизованного пользователя:
```bash
php artisan telegram:clear-cache
```
При необходимости команду можно указать для выполнения в планировщике заданий.

<br>

## Дополнительная информация

- [Laravel Docs](https://laravel.com/docs)
- [Laravel Package Development](https://laravelpackage.com)
- [Laravel Package Toolkit](https://packages.tools/testbench)
- [Telegram](https://telegram.org)
- [Telegram Bot API](https://core.telegram.org/bots/api)
