@extends('guest.layout.base-template')

@include('helpers.robots-no-index')

@section('title',       __('seo.title.subIdxDetail'))
@section('description', __('seo.description.subIdxDetail'))
@section('keywords',    __('seo.keywords.subIdxDetail'))

@section('content')

    <h1>Sub/Idx Download</h1>
    <p>
        The srt files are being extracted from the sub/idx file.
        This page will update automatically.
        Extracting a language can take a few minutes, please be patient.
    </p>


    <p class="mt-4">
        Extracting srt files from <strong>{{ $originalName }}</strong>
    </p>

    <div class="block lg:hidden my-8">
        @include('helpers.ads.result-page-large-mobile-banner')
    </div>


    <div class="flex my-8">
        <sub-idx-languages page-id="{{ $pageId }}"></sub-idx-languages>

        <div class="mx-auto lg:block hidden">
            @if($languageCount > 10)
                @include('helpers.ads.result-page-large-skyscraper')
            @else
                @include('helpers.ads.result-page-large-rectangle')
            @endif
        </div>
    </div>


    <button type="button" class="tool-btn" href="{{ route('subIdx') }}">Back to tool</button>


@endsection
