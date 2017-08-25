<?php

namespace App\Providers;

use App\Utils\FileName;
use App\Utils\TempFile;
use Illuminate\Support\ServiceProvider;

class UtilsProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton('TextEncoding', function($app) {
           return new \App\Utils\Text\TextEncoding();
        });

        $this->app->singleton('TextFileIdentifier', function($app) {
            return new \App\Utils\Text\TextFileIdentifier(
                app('TextEncoding')
            );
        });

        $this->app->singleton('TextFileReader', function($app) {
            return new \App\Utils\Text\TextFileReader(
                app('TextFileIdentifier'),
                app('TextEncoding')
            );
        });

        $this->app->bind('FileName', function($app, $args) {
            return new FileName();
        });

        $this->app->bind('TempFile', function($app, $args) {
            return new TempFile();
        });
    }

}
