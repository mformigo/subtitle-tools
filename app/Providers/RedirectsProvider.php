<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RedirectsProvider extends ServiceProvider
{
    protected $redirects = [
        // (old) url               // destination route name
        'format-converter'         => 'convertToSrt',
        'convert-to-srt'           => 'convertToSrt',
        'fo...'                    => 'convertToSrt',
        'convert-to-srt-on...'     => 'convertToSrt',
        'c...'                     => 'convertToSrt',
        'tools'                    => 'home',
        'chinese-to-pinyin'        => 'pinyin',
        'subtitle-shift'           => 'shift',
        'partial-subtitle-shifter' => 'shiftPartial',
        'multi-subtitle-shift'     => 'shiftPartial',
        'convert-to-utf8'          => 'convertToUtf8',
    ];

    public function boot()
    {
        foreach($this->redirects as $url => $destinationRouteName) {
            Route::any($url, function() use ($destinationRouteName) {
                return redirect(route($destinationRouteName), 301);
            });
        }
    }

    public function register()
    {

    }
}
