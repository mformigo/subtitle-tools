<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RedirectsProvider extends ServiceProvider
{
    public function boot()
    {
        $redirects = [
            'format-converter' => 'convertToSrt',
            'convert-to-srt'   => 'convertToSrt',
            'fo...'            => 'convertToSrt',
            'tools'            => 'home',
            //'chinese-to-pinyin'        => 'make-chinese-pinyin-subtitles',
            //'subtitle-shift'           => 'subtitle-sync-shifter',
            //'partial-subtitle-shifter' => 'partial-subtitle-sync-shifter',
            //'multi-subtitle-shift'     => 'partial-subtitle-shifter',
        ];

        foreach($redirects as $url => $destinationRouteName) {
            Route::get($url, function() use ($destinationRouteName) {
                return redirect(route($destinationRouteName), 301);
            });
        }
    }

    public function register()
    {

    }
}
