<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // These middleware are run during every request to your application.
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    // The application's route middleware groups.
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            \App\Http\Middleware\CountUploadedFiles::class,
            \App\Http\Middleware\EnhanceUploadedFiles::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    // These middleware may be assigned to groups or used individually.
    protected $routeMiddleware = [
        'auth'          => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic'    => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'      => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'           => \Illuminate\Auth\Middleware\Authorize::class,
        'throttle'      => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'swap-sub-and-idx' => \App\Http\Middleware\SwapSubAndIdx::class,
        'extract-archives' => \App\Http\Middleware\ExtractArchives::class,
        'check-file-size'  => \App\Http\Middleware\CheckFileSize::class,
    ];

}
