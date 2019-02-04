<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

    public function map()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/guest-web-routes.php'));

        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/redirects.php'));

        Route::middleware('api')
            ->namespace($this->namespace.'\Api')
            ->name('api.')
            ->prefix('api/v1/')
            ->group(base_path('routes/guest-api-routes.php'));

        Route::middleware(['web', 'auth'])
            ->namespace($this->namespace.'\Admin')
            ->name('admin.')
            ->prefix('st-admin')
            ->group(base_path('routes/admin-web-routes.php'));
    }
}
