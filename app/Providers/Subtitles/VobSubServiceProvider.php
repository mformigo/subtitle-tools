<?php

namespace App\Providers\Subtitles;

use App\Events\ExtractingSubIdxLanguageChanged;
use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use App\Subtitles\VobSub\VobSub2Srt;
use App\Subtitles\VobSub\VobSub2SrtInterface;
use App\Subtitles\VobSub\VobSub2SrtMock;
use Illuminate\Support\ServiceProvider;

class VobSubServiceProvider extends ServiceProvider
{
    public function boot()
    {
        SubIdxLanguage::updated(function ($subIdxLanguage) {
            ExtractingSubIdxLanguageChanged::dispatch($subIdxLanguage);
        });
    }

    public function register()
    {
        $this->app->bind(VobSub2SrtInterface::class, function ($app, $args) {
                return new VobSub2Srt(
                //return new VobSub2SrtMock(
                    $args['path'],
                    $args['subIdx'] ?? null
                );
        });
    }
}
