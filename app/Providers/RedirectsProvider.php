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
        'tools'                    => 'home',
        'chinese-to-pinyin'        => 'pinyin',
        'subtitle-shift'           => 'shift',
        'partial-subtitle-shifter' => 'shiftPartial',
        'multi-subtitle-shift'     => 'shiftPartial',
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
