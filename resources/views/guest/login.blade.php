@extends('guest.layout.base-template')

@section('title',       'Login | Subtitle Tools')
@section('description', '')
@section('keywords',    'login, yo')

@include('helpers.disconnect-echo')

@section('content')


    <h1>Login</h1>

    <form id="login" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <label class="block my-2 font-bold">
            Username
            <input class="block field" type="text" name="username" value="{{ old('username') }}" required autofocus>
        </label>

        <label class="block my-2 font-bold">
            Password
            <input class="block field" type="password" name="password" required>
        </label>

        <input id="remember" class="hidden" type="checkbox" name="remember" checked>

        <button type="submit" class="tool-btn float-none">Login</button>

    </form>

@endsection
