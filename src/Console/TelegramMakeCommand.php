<?php

namespace ProgLib\Telegram\Bot\Console;

use Illuminate\Console\Command as BaseCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class TelegramMakeCommand extends BaseCommand {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $signature = 'telegram:make
                            {--controllers : Создаёт необходимые контроллеры для работы веб-перехватчика.}
                            {--routes : Создаёт необходимые маршруты для работы веб-перехватчика.}';

    /**
     * @inheritDoc
     */
    protected $description = 'Выполняет создание необходимых файлов для бота «Telegram».';

    #endregion

    /**
     * ...
     *
     * @return void
     */
    public function handle() {
        if ($this->option('controllers'))
            $this->controllers();

        elseif ($this->option('routes'))
            $this->routes();

        else {
            $this->controllers();
            $this->routes();
        }
    }

    /**
     * ...
     *
     * @return void
     */
    protected function controllers() {
        $path_controllers = app_path(implode(DIRECTORY_SEPARATOR, [ 'Http', 'Controllers', 'Bots' ]));
        $path_stubs       = implode(DIRECTORY_SEPARATOR, [ __DIR__, '..', '..', 'stubs', 'controllers' ]);

        if (!is_dir($path_controllers))
            mkdir($path_controllers, 0755, true);

        $filesystem = new Filesystem;

        collect($filesystem->allFiles($path_stubs))
            ->each(function (SplFileInfo $file) use ($path_controllers, $filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    implode(DIRECTORY_SEPARATOR, [ $path_controllers, Str::replaceLast('.stub', '.php', $file->getFilename()) ])
                );
            });

        $this->info('Контроллеры сгенерированы успешно.');
    }

    /**
     * ...
     *
     * @return void
     */
    protected function routes() {
        $path_routes = base_path('routes');
        $path_stubs  = implode(DIRECTORY_SEPARATOR, [ __DIR__, '..', '..', 'stubs', 'routes' ]);

        if (!is_dir($path_routes))
            mkdir($path_routes, 0755, true);

        $filesystem = new Filesystem;

        collect($filesystem->allFiles($path_stubs))
            ->each(function (SplFileInfo $file) use ($path_routes, $filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    implode(DIRECTORY_SEPARATOR, [ $path_routes, Str::replaceLast('.stub', '.php', $file->getFilename()) ])
                );
            });

        $this->info('Маршруты сгенерированы успешно.');
    }
}
