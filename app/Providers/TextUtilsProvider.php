<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TextUtilsProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton('Utils\TextEncoding', function($app) {
           return new \App\Utils\TextEncoding();
        });

        $this->app->singleton('Utils\TextFileIdentifier', function($app) {
            return new \App\Utils\TextFileIdentifier(
                app('Utils\TextEncoding')
            );
        });
    }

}
