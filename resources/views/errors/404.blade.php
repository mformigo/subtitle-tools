@extends('guest.layout.base-template')

@section('title',       __('seo.title.404'))
@section('description', __('seo.description.404'))
@section('keywords',    __('seo.keywords.404'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') 404 - Page not Found @endslot

    @endcomponent

    <div class="container">

        <a href="{{ route('home') }}" class="btn">Back to homepage</a>

    </div>

@endsection
