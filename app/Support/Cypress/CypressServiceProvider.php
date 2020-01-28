<?php

namespace App\Support\Cypress;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CypressServiceProvider extends ServiceProvider
{
    private $routeConfig = [
        'namespace' => 'App\Support\Cypress',
        'prefix' => 'api/__cypress',
    ];

    public function boot()
    {
        Route::group($this->routeConfig, function () {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        });
    }
}
