<?php

namespace App\Providers\Framework;

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
    }

    protected function registerRouteMacros()
    {
        Route::macro('fileGroupTool', function($routeName, $controller, $slug) {
            Route::prefix($slug)->group(function() use ($controller, $routeName) {
                Route::get('/')->uses($controller.'@index')->name($routeName);
                Route::post('/')->uses($controller.'@post');
                Route::get('/{urlKey}')->uses($controller.'@result')->name($routeName.'Result');
                Route::post('/{urlKey}/{id}')->uses($controller.'@download')->name($routeName.'Download');
            });
        });
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    protected function mapApiRoutes()
    {
        Route::middleware('api')
             ->namespace($this->namespace . '\Api')
             ->group(base_path('routes/api.php'));
    }



}
