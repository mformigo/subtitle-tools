<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="@yield('description')" />
        <meta name="keywords" content="@yield('keywords')" />

        <link rel="canonical" href="{{ URL::current() }}" />

        <title>@yield('title')</title>

        @stack('head')

        <script>
            <?php
                echo 'window.Laravel = ' . json_encode([
                    'csrf_token' => csrf_token(),
                    'pusherKey'       => env('PUSHER_APP_KEY'),
                    'pusherCluster'   => env('PUSHER_APP_CLUSTER'),
                    'pusherEncrypted' => env('APP_HTTPS'),
                ]);
            ?>
        </script>

        <link href="{{ mix('css/app.css') }}" rel="stylesheet" />
        <link href="/css/flags.css" rel="stylesheet" />

        @if(App::environment('production'))
            <script>
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

                ga('create', 'UA-85344990-2', 'auto');
                ga('send', 'pageview');
            </script>
        @endif

    </head>
    <body>
        <div id="app">
            @yield('content')
        </div>

        <script src="{{ mix('js/app.js') }}"></script>

        @stack('inline-footer-scripts')

    </body>
</html>
