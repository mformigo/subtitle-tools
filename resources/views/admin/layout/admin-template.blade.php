<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="canonical" href="{{ URL::current() }}" />

    <title>Admin | Subtitle Tools</title>

    @stack('head')

    <link rel="icon" type="image/png" href="/images/favicon.png" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{{ mix('css/admin.css') }}" />

</head>
<body>

    @include('admin.layout.header')

    <div id="app" class="admin">
        @yield('content')
    </div>

    @stack('footer')

</body>
</html>
