@extends('base-template')

@section('title',       __('seo.title.convertToUtf8'))
@section('description', __('seo.description.convertToUtf8'))
@section('keywords',    __('seo.keywords.convertToUtf8'))

@section('content')

    @component('components.page-intro')

        @slot('title') Convert files to UTF-8 @endslot

        This tool converts any text or subtitle file to UTF-8 encoding.
        If your subtitle file displays as random, unreadable characters, this tool will probably fix them.
        The other tools on this website convert the files to UTF-8 by default.

    @endcomponent

    @component('components.tool-form')

        @slot('title') Select files to convert to UTF-8 @endslot

        @slot('formats') Supported formats: any text file @endslot

        @slot('buttonText') Convert to UTF-8 @endslot

    @endcomponent

@endsection
