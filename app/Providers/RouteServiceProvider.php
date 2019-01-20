<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use RuntimeException;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

    public function map()
    {
        $this->registerRouteMacros();

        $this->registerRoutes();
    }

    private function registerRouteMacros()
    {
        Route::macro('fileGroupTool', function ($routeName, $controller, $slug) {
            if (! in_array($routeName, config('st.tool_routes'))) {
                throw new RuntimeException('This route is not defined in the config');
            }

            Route::prefix($slug)->group(function () use ($controller, $routeName) {
                Route::get('/',               ['uses' => "$controller@index",    'as' => "$routeName"]);
                Route::post('/',              ['uses' => "$controller@post",     'as' => "$routeName.post"]);
                Route::get('/{urlKey}',       ['uses' => "$controller@result",   'as' => "$routeName.result"]);
                Route::post('/{urlKey}/{id}', ['uses' => "$controller@download", 'as' => "$routeName.download"]);

                Route::get('/{urlKey}/{id}', function ($urlKey, $id) use ($routeName) {
                    return redirect()->route("$routeName.result", $urlKey);
                });
            });
        });
    }

    private function registerRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/guest-web-routes.php'));

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
