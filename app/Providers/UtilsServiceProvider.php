<?php

namespace App\Providers;

use App\Support\Utils\FileName;
use App\Support\Utils\TempDir;
use App\Support\Utils\TempFile;
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
