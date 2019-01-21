<!doctype html>
<html lang="en" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="canonical" href="{{ URL::current() }}" />

        <title>Admin | Subtitle Tools</title>

        @stack('head')

        <link rel="icon" type="image/png" href="/favicon.png" />

        <link rel="stylesheet" type="text/css" href="{{ mix('css/main.css') }}" />

    </head>

    <body class="bg-grey-lighter h-full">
        <div id="app" class="flex h-full">
            <div class="relative w-48 mr-6 border-r bg-white">
                @include('admin.layout.header')
            </div>
            <div class="flex-grow mt-6 mr-6">
                @yield('content')
            </div>
        </div>

        @stack('footer')
    </body>
</html>
