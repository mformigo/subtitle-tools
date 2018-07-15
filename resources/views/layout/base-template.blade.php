<!doctype html>
<html lang="en">
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

        <link rel="icon" type="image/png" href="/images/favicon.png" />

        <link rel="stylesheet" type="text/css" href="{{ mix('css/main.css') }}" />

        @if(App::environment('production'))
            <script async defer src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({
                    google_ad_client: "ca-pub-8027891891391991",
                    enable_page_level_ads: true
                });

                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

                ga('create', 'UA-85344990-2', 'auto');
                ga('send', 'pageview');
            </script>
        @endif

    </head>
    <body class="font-sans relative overflow-hidden overflow-y-scroll">

        @include('layout.header')

        @include('layout.global-notification')

        <div id="app" class="container mx-auto px-4">
            @yield('content')
        </div>

        @include('layout.footer')

        <script>
            function adBlockDetected() {
                var elements = document.getElementsByClassName("anti-adblock-ad");

                for (var i = 0; i < elements.length; i++) {
                    elements[i].classList.remove("hidden");
                }
            }

            (typeof blockAdBlock === "undefined") ? adBlockDetected() : blockAdBlock.onDetected(adBlockDetected);
        </script>

        <script src="{{ mix('js/app.js') }}"></script>
        @stack('footer')

    </body>
</html>
