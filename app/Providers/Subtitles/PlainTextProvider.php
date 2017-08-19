<?php

namespace App\Providers\Subtitles;

use App\Events\FileGroupChanged;
use App\Models\FileGroup;
use App\Subtitles\TextFileFormat;
use Illuminate\Support\ServiceProvider;

class PlainTextProvider extends ServiceProvider
{
    public function boot()
    {
        FileGroup::updated(function ($fileGroup) {
            event(new FileGroupChanged($fileGroup));
        });
    }

    public function register()
    {
        $this->app->bind('TextFileFormat', function($app, $args) {
                return new TextFileFormat();
        });
    }
}
