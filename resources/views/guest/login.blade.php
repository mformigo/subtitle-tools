@extends('guest.layout.base-template')

@section('title',       'Login | Subtitle Tools')
@section('description', '')
@section('keywords',    'login, yo')

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Login @endslot

    @endcomponent


    <div class="container">
        <form id="login" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>

            <input id="remember" class="hidden" type="checkbox" name="remember" checked>

            <button type="submit" class="btn">Login</button>

        </form>
    </div>

@endsection
