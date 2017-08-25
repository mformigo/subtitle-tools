@extends('base-template')

@include('helpers.robots-no-index')

@section('title',       __('seo.title.subIdxDetail'))
@section('description', __('seo.description.subIdxDetail'))
@section('keywords',    __('seo.keywords.subIdxDetail'))

@section('content')

    @component('components.page-intro')
        @slot('title') Sub/Idx Download @endslot

        The srt files are being extracted from the sub/idx file.
        This page will update automatically.
        Extracting a language can take a few minutes, please be patient.
    @endcomponent

    <div class="container">

        <p>
            Extracting srt files from <strong>{{ $originalName }}</strong>
        </p>

        <sub-idx-languages page-id="{{ $pageId }}"></sub-idx-languages>

        <br/>
        <br/>
        <a class="btn" href="{{ route('subIdx') }}">Back to tool</a>

    </div>

@endsection
