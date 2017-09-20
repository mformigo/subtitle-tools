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
        $this->app->bind('FileName', function($app, $args) {
            return new FileName();
        });

        $this->app->bind('TempFile', function($app, $args) {
            return new TempFile();
        });
    }

}
