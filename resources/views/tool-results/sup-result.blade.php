@extends('layout.base-template')

@include('helpers.robots-no-index')

@section('title',       'Download sup to srt file | Subtitle Tools')
@section('description', 'Download your srt file when it is done processing')
@section('keywords',    'sup, download')

@section('content')

    <h1>Sup to Srt Download</h1>
    <p>
        Your sup file is being converted.
        Once it is done the page will update automatically.
    </p>


    <div class="inline-block mt-8 p-3 bg-yellow-lighter">
        <strong class="block mb-1">This tool is still in beta!</strong>
        It might not work correctly yet
    </div>


    <p class="mt-4">
        Extracting srt files from <strong>{{ $originalName }}</strong>
        using <strong>{{ __('languages.tesseract.'.$ocrLanguage) }}</strong> as the OCR language
    </p>


    <div class="block lg:hidden my-8">
        @include('helpers.ads.result-page-large-mobile-banner')
    </div>


    <div class="flex my-8">
        <sup-job url-key="{{ $urlKey }}"></sup-job>

        <div class="mx-auto lg:block hidden">
            @include('helpers.ads.result-page-large-rectangle')
        </div>
    </div>


    <a class="tool-btn inline-block" href="{{ route('sup') }}">Back to tool</a>


@endsection
