<?php

namespace App\Providers;

use App\Utils\Support\FileName;
use App\Utils\Support\TempDir;
use App\Utils\Support\TempFile;
use Illuminate\Support\ServiceProvider;

class UtilsServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('FileName', function ($app, $args) {
            return new FileName();
        });

        $this->app->bind('TempFile', function ($app, $args) {
            return new TempFile();
        });

        $this->app->bind('TempDir', function ($app, $args) {
            return new TempDir();
        });
    }

}
