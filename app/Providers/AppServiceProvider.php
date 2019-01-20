<?php

namespace App\Providers;

use App\Events\FileGroupChanged;
use App\Models\FileGroup;
use App\Subtitles\TextFileFormat;
use App\Subtitles\VobSub\VobSub2Srt;
use App\Subtitles\VobSub\VobSub2SrtInterface;
use App\Support\Utils\FileHash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        FileGroup::updated(function ($fileGroup) {
            FileGroupChanged::dispatch($fileGroup);
        });

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
            return new FileHash();
        });

        $this->app->bind('TextFileFormat', function ($app, $args) {
            return new TextFileFormat();
        });

        $this->app->singleton(VobSub2SrtInterface::class, VobSub2Srt::class);
    }
}
