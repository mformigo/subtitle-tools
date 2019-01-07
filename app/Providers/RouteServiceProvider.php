<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->registerRouteMacros();

        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapWebAdminRoutes();
    }

    protected function registerRouteMacros()
    {
        Route::macro('fileGroupTool', function ($routeName, $controller, $slug) {
            if (! in_array($routeName, config('st.tool_routes'))) {
                throw new \LogicException('This route is not defined in the config');
            }

            Route::prefix($slug)->namespace('FileJobs')->group(function () use ($controller, $routeName) {
                Route::get('/',               ['uses' => $controller.'@index',    'as' => $routeName]);
                Route::post('/',              ['uses' => $controller.'@post']);
                Route::get('/{urlKey}',       ['uses' => $controller.'@result',   'as' => $routeName.'.result']);
                Route::post('/{urlKey}/{id}', ['uses' => $controller.'@download', 'as' => $routeName.'.download']);

                Route::get('/{urlKey}/{id}', function ($urlKey, $id) use ($routeName) {
                    return redirect()->route($routeName.'.result', $urlKey);
                });
            });
        });
    }

    protected function mapApiRoutes()
    {
        Route::middleware('api')
            ->prefix('api/v1/')
            ->namespace($this->namespace.'\Api')
            ->group(base_path('routes/api-routes.php'));
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web-routes.php'));
    }

    protected function mapWebAdminRoutes()
    {
        Route::middleware(['web', 'auth'])
             ->namespace($this->namespace.'\Admin')
             ->prefix('st-admin')
             ->group(base_path('routes/web-admin-routes.php'));
    }
}
