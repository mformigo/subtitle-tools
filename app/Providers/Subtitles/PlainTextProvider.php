<?php

namespace App\Providers\Subtitles;

use App\Models\SubIdx;
use App\Subtitles\TextFileFormat;
use App\Subtitles\VobSub\VobSub2Srt;
use App\Subtitles\VobSub\VobSub2SrtInterface;
use App\Subtitles\VobSub\VobSub2SrtMock;
use Illuminate\Support\ServiceProvider;

class PlainTextProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('TextFileFormat', function($app, $args) {
                return new TextFileFormat();
        });
    }
}
