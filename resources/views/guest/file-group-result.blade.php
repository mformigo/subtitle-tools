@extends('guest.layout.base-template')

@include('helpers.robots-no-index')

@section('title',       __('seo.title.fileGroupResult'))
@section('description', __('seo.description.fileGroupResult'))
@section('keywords',    __('seo.keywords.fileGroupResult'))

@section('content')

    <h1>Download</h1>
    <p>
        Your files are being processed.
        Once they are done the page will update automatically.
    </p>


    @if($fileCount > 1)
        <h3 class="mb-2">Download zip file</h3>
        <file-group-archive url-key="{{ $urlKey }}"></file-group-archive>
    @endif


    <div class="block lg:hidden my-8">
        @include('helpers.ads.result-page-large-mobile-banner')
    </div>


    <div class="flex my-8">
        <file-group-jobs url-key="{{ $urlKey }}"></file-group-jobs>

        <div class="mx-auto lg:block hidden">
            @if($fileCount > 15)
                @include('helpers.ads.result-page-large-skyscraper')
            @else
                @include('helpers.ads.result-page-large-rectangle')
            @endif
        </div>
    </div>

    <a class="tool-btn inline-block" href="{{ $returnUrl }}">Back to tool</a>


@endsection
