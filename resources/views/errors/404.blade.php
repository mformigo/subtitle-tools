@extends('layout.base-template')

@section('title',       __('seo.title.404'))
@section('description', __('seo.description.404'))
@section('keywords',    __('seo.keywords.404'))

@section('content')

    <h1>404 - Page not Found</h1>
    <p>
        <a href="{{ route('home') }}">Back to homepage</a>
    </p>

@endsection
