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

Установка:
```bash
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan telegram:webhook bot --setup
php artisan telegram:webhook bot --set-commands
```

<br>

Поддерживаемые параметр конфигурации:
```dotenv
# Параметры отправки сообщения
TELEGRAM_BOT_PARSE_MODE="MarkdownV2"
TELEGRAM_BOT_DISABLE_PREVIEW=true

# Отладка запросов к API
TELEGRAM_BOT_DEBUG=false

# Ограничение по количеству запросов на веб-перехватчик
TELEGRAM_BOT_THROTTLE=false
TELEGRAM_BOT_THROTTLE_ATTEMPTS=50
TELEGRAM_BOT_THROTTLE_DURING=1

# Ссылки на ресурсы мессенджера
TELEGRAM_URL_SITE="https://telegram.org"
TELEGRAM_URL_MESSENGER="https://t.me"
TELEGRAM_URL_API="https://api.telegram.org"
```

<br>

## Routing

...

<br>

## Cache

Для работы буфера реализовано два новых драйвера: `database` (используется по умолчанию) и `file`.
При необходимости можно добавить пользовательский стандартными средствами фреймворка.

<br>

Конфигурация:
```dotenv
# Драйвер буфера. По умолчанию "database".
TELEGRAM_CACHE_DRIVER="database"
```

<br>

Использование фасада:

```php
use ProgLib\Telegram\Bot\Facades\Cache;

// Сохранение
$result = Cache::put('key', 'value', config('cache.ttl'));

// Получение
$value  = Cache::get('key', 'default');

// Удаление
$result = Cache::forget('key');
```

<br>

Использование вспомогательного метода:
```php

// Сохранение
$result = tb_cache()->put('key', 'value', config('cache.ttl'));
$result = tb_cache([
    'key_1' => 'value',
    'key_2' => 'value'
]);

// Получение
$value  = tb_cache('key', 'default');

// Удаление
$result = tb_cache()->forget('key');
```

<br>

В случае переполнения хранилища буфера существует команда для полной её очистки независимо от авторизованного пользователя:
```bash
php artisan telegram:clear-cache
```
При необходимости команду можно указать для выполнения в планировщике заданий.

<br>

## Logging

<br>

Конфигурация:
```dotenv
# Драйвер журанала. По умолчанию "file".
TELEGRAM_LOG_DRIVER="file"

# Уровень журанала. По умолчанию "debug".
TELEGRAM_LOG_LEVEL="debug"
```

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
