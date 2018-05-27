@extends('layout.base-template')

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


    <div class="flex my-8 flex-col-reverse md:flex-row">
        <sub-idx-languages page-id="{{ $pageId }}"></sub-idx-languages>

        <div class="lg:mx-auto md:ml-auto md:mb-0 mb-6">
            @include('helpers.ads.result-page-large-rectangle')
        </div>
    </div>


    <a class="tool-btn inline-block" href="{{ route('subIdx') }}">Back to tool</a>


@endsection
