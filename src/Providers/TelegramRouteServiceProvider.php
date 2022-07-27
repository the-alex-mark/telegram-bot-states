<?php

namespace ProgLib\Telegram\Bot\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class TelegramRouteServiceProvider extends ServiceProvider {

    #region Properties

    /**
     * @inheritDoc
     */
    protected $namespace = 'App\Http\Controllers\Bots';

    #endregion

    /**
     * @inheritDoc
     */
    public function map() {

        if (file_exists(base_path('routes/bots.php'))) {
            Route::group(
                [ 'namespace' => $this->namespace, 'prefix' => 'bot' ],
                base_path('routes/bots.php')
            );
        }
    }
}
