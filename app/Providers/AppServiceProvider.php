<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('dropbox', function ($app, $config) {
            $client = new DropboxClient(
                config('filesystems.disks.dropbox.key')
            );

            return new Filesystem(new DropboxAdapter($client));
        });
    }

    public function register()
    {
        $this->app->singleton('FileHash', function () {
            return new \App\Utils\Support\FileHash();
        });
    }
}
