# Telegram Bot States for Laravel

Реализует состояния бота для мессенджера «**Telegram**».

<br>

## Использование

```bash
composer require the_alex_mark/telegram-bot-states
```

<br>

Публикация ресурсов:
```bash

# Миграции
php artisan vendor:publish --provider="ProgLib\Telegram\Bot\Providers\TelegramStatesServiceProvider" --tag="telegram.bot.migrations"

# Файлы локализации
php artisan vendor:publish --provider="ProgLib\Telegram\Bot\Providers\TelegramStatesServiceProvider" --tag="telegram.bot.translations"
```

<br>

## Cache

Для работы буфера реализовано два новых драйвера: `database` (используется по умолчанию) и `file`.
При необходимости можно добавить пользовательский стандартными средствами фреймворка.

<br>

Использование фасада:
```php
use ProgLib\Telegram\Bot\Facades\TelegramCache;

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

## Дополнительные модули

- [Telegram Bot SDK](https://github.com/irazasyed/telegram-bot-sdk)

<br>

## Дополнительная информация

- [Laravel Docs](https://laravel.com/docs)
- [Laravel Package Development](https://laravelpackage.com)
- [Laravel Package Toolkit](https://packages.tools/testbench)
- [Telegram](https://telegram.org)
- [Telegram Bot API](https://core.telegram.org/bots/api)
