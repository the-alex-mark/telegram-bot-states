<?php

namespace ProgLib\Telegram\Console;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Helper\TableCell;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Artisan\WebhookCommand as BaseWebhookCommand;
use Telegram\Bot\Objects\BotCommand;
use Telegram\Bot\Objects\WebhookInfo;
use Telegram\Bot\TelegramRequest;

class TelegramWebhookCommand extends BaseWebhookCommand {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $signature = 'telegram:webhook {bot? : Имя бота, определенное в конфигурационном файле.}
                            {--all : Для выполнения действий со всеми вашими ботами.}
                            {--setup : Задаёт маршрут исходящего веб-перехватчика на сервере «Telegram».}
                            {--remove : Удаляет маршрут исходящего веб-перехватчика на сервере «Telegram».}
                            {--info : Отображает информацию об веб-перехватчике.}
                            {--set-commands : Задаёт перечень команд бота}
                            {--remove-commands : Задаёт перечень команд бота}';

    /**
     * @inheritDoc
     */
    protected $description = 'Настройка веб-перехватчика.';

    #endregion

    #region Helpers

    /**
     * @inheritDoc
     */
    protected function mapBool($value) {
        return $value ? 'Да' : 'Нет';
    }

    /**
     * @inheritDoc
     */
    protected function makeWebhookInfoResponse(WebhookInfo $response, string $bot) {
        $rows = $response
            ->map(function ($value, $key) {
                $key = Str::title(str_replace('_', ' ', $key));

                if (is_bool($value))
                    $value = $this->mapBool($value);

                if (is_array($value))
                    $value = implode(', ', $value);

                return compact('key', 'value');
            })
            ->toArray();

        $this->table([
            [ new TableCell('Бот: ' . $bot, [ 'colspan' => 2 ]) ], [ 'Параметр', 'Значение' ] ],
            $rows
        );
    }

    #endregion

    /**
     * @inheritDoc
     */
    public function handle() {
        parent::handle();

        if ($this->option('set-commands'))
            $this->setCommands();

        if ($this->option('remove-commands'))
            $this->removeCommands();
    }

    /**
     * @inheritDoc
     */
    protected function setupWebhook() {
        $route = route('bot.telegram.webhook', [ 'token' => data_get($this->config, 'token') ]);
        $url   = data_get($this->config, 'webhook', $route);

        if (!URL::isValidUrl($url))
            throw new RuntimeException('Адрес веб-перехватчика задан некорректно.');

        if (parse_url($url, PHP_URL_SCHEME) === 'http')
            throw new RuntimeException('Требуется защищённое соединение по HTTPS.');

        // Параметры работы веб-перехватчика
        $additionally = config('telegram.webhook', []);
        $params       = array_merge($additionally, [
            'url' => $url,
            'allowed_updates' => json_encode([])
        ]);

        // Указание сертификата для безопасных запросов
        $certificate = data_get($this->config, 'certificate', false);
        if ($certificate)
            $params['certificate'] = $certificate;

        if ($this->telegram->setWebhook($params)) {
            $this->info('Адрес веб-перехватчика успешно установлен!');
            return true;
        }

        throw new RuntimeException('Не удалось установить адрес веб-перехватчика.' . PHP_EOL . 'Проверьте правильность указанной конфигурации или повторите попытку через некоторое время.');
    }

    /**
     * @inheritDoc
     */
    protected function removeWebHook() {
        if ($this->confirm("Вы уверены, что хотите отключить веб-перехватчик для {$this->config['bot']}?")) {
            $this->info('Отключение ...');

            if ($this->telegram->removeWebhook()) {
                $this->info('Веб-перехватчик успешно отключен!');
                return;
            }

            throw new RuntimeException('Не удалось отключить веб-перехватчик');
        }
    }

    /**
     * ...
     *
     * @throws TelegramSDKException
     */
    protected function setCommands() {
        $commands = $this->telegram->getCommands();
        $except   = [ 'start', 'refresh', 'test' ];
        $list     = [];

        /**
         * @var string  $name
         * @var Command $handler
         */
        foreach ($commands as $name => $handler) {
            if (!in_array($name, $except)) {
                $list[] = BotCommand::make([
                    'command'     => $handler->getName(),
                    'description' => $handler->getDescription()
                ]);
            }
        }

        $result = $this->telegram->setMyCommands([
            'commands' => $list
        ]);

        if ($result) {
            $this->info('Перечень команд бота успешно задан!');
            return true;
        }

        throw new RuntimeException('Не удалось задать команды бота.');
    }

    /**
     * ...
     *
     * @throws TelegramSDKException
     */
    protected function removeCommands() {
        $request = new TelegramRequest(
            $this->telegram->getAccessToken(), 'GET', 'deleteMyCommands', [],
            $this->telegram->isAsyncRequest()
        );

        $request
            ->setTimeOut($this->telegram->getTimeOut())
            ->setConnectTimeOut($this->telegram->getConnectTimeOut());

        $response = $this->telegram->getClient()->sendRequest($request);

        // Получение параметров ответа
        $body = $response->getDecodedBody();

        if (isset($body['ok']) && $body['ok'] === true) {
            $this->info('Перечень команд бота успешно очищен!');
            return true;
        }

        throw new RuntimeException('Не удалось очистить команды бота.');
    }
}
