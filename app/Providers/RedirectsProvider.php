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
            'chinese-to-pinyin'        => 'pinyin',
            'subtitle-shift'           => 'shift',
            'partial-subtitle-shifter' => 'shiftPartial',
            'multi-subtitle-shift'     => 'shiftPartial',
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
