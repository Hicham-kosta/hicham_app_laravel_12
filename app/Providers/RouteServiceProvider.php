<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after login.
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->routes(function () {

            /*
            |--------------------------------------------------------------------------
            | API Routes
            |--------------------------------------------------------------------------
            | These routes are loaded with the "api" middleware group
            | and are prefixed automatically with /api
            |
            */
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));


            /*
            |--------------------------------------------------------------------------
            | Web Routes
            |--------------------------------------------------------------------------
            | These routes are loaded with the "web" middleware group
            |
            */
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}