@extends('layout.base-template')

@section('title', __('seo.title.fileGroupResult'))
@section('description', __('seo.description.fileGroupResult'))
@section('keywords', __('seo.keywords.fileGroupResult'))

@include('helpers.robots-no-index')

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Download</h1>
    <p>
        Your files are being processed.
        Once they are done the page will update automatically.
    </p>


    @if($fileCount > 1)
        <h3>Download zip file</h3>
        <p class="max-w-xs text-sm leading-normal mb-4">
            Download the converted files as a single zip file.
            The zip file does not include failed files.
        </p>

        <file-group-archive url-key="{{ $urlKey }}"></file-group-archive>
    @endif


    <div class="flex my-8 flex-col md:flex-row">
        <file-group-jobs url-key="{{ $urlKey }}"></file-group-jobs>

        <div class="mx-auto mt-8 md:mt-0">
            @include('helpers.ads.result-page-large-rectangle')
        </div>
    </div>

    <a class="tool-btn inline-block" href="{{ $returnUrl }}">Back to tool</a>


@endsection
