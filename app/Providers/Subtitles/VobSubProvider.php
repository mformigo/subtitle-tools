<?php

namespace App\Providers\Subtitles;

use App\Models\SubIdx;
use App\Subtitles\VobSub\VobSub2Srt;
use App\Subtitles\VobSub\VobSub2SrtInterface;
use Illuminate\Support\ServiceProvider;

class VobSubProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind(VobSub2SrtInterface::class, function($app, $args) {
                return new VobSub2Srt(
                    $args['path'],
                    $args['subIdx'] ?? null
                );
        });
    }
}
