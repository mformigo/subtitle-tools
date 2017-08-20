<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            <?php
                echo 'window.Laravel = ' . json_encode([
                    'csrf_token' => csrf_token(),
                    'pusherKey' => env('PUSHER_APP_KEY'),
                    'pusherCluster' => env('PUSHER_APP_CLUSTER'),
                    'pusherEncrypted' => env('APP_HTTPS'),
                ]);
            ?>
        </script>

        <link href="{{ mix('css/app.css') }}" rel="stylesheet" />
        <link href="/css/flags.css" rel="stylesheet" />

        <title>ST</title>
    </head>
    <body>
        <div id="app">
            @yield('content')
        </div>

        <script src="{{ mix('js/app.js') }}"></script>

        @stack('inline-footer-scripts')

    </body>
</html>
