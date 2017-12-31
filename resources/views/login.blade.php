@extends('layout.base-template')

@section('title',       'Login | Subtitle Tools')
@section('description', '')
@section('keywords',    'login, yo')

@include('helpers.disconnect-echo')

@section('content')

    <h1>Login</h1>

    <form method="post" action="{{ route('login') }}">
        {{ csrf_field() }}

        <label class="block my-2 font-bold">
            Username
            <input class="block field" type="text" name="username" value="{{ old('username') }}" required autofocus>
        </label>

        <label class="block my-2 font-bold">
            Password
            <input class="block field" type="password" name="password" required>
        </label>

        <input class="hidden" type="checkbox" name="remember" checked>

        <button type="submit" class="tool-btn">Login</button>

    </form>

@endsection
