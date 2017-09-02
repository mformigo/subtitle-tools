@extends('layout.base-template')

@include('helpers.robots-no-index')

@section('title',       __('seo.title.fileGroupResult'))
@section('description', __('seo.description.fileGroupResult'))
@section('keywords',    __('seo.keywords.fileGroupResult'))

@section('content')

    @component('components.page-intro')

        @slot('title') Download @endslot

        Your files are being processed.
        Once they are done the page will update automatically.

    @endcomponent

    <div class="container">

        @if($fileCount > 5)
            <file-group-archive url-key="{{ $urlKey }}"></file-group-archive>
        @endif

        <file-group-jobs url-key="{{ $urlKey }}"></file-group-jobs>


        <br/>
        <br/>

        <a class="btn" href="{{ $returnUrl }}">Back to tool</a>

    </div>

@endsection
