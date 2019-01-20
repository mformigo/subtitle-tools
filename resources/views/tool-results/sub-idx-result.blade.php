@extends('layout.base-template')

@include('helpers.robots-no-index')

@section('title', __('seo.title.subIdxDetail'))
@section('description', __('seo.description.subIdxDetail'))

@section('content')
    <h1>Sub/Idx to Srt</h1>
    <p class="max-w-md mb-8">
        Select the languages you want to extract.
        This page will update automatically.
        <br>
        <br>
        Sub/idx name: <strong>{{ $originalName }}</strong>
    </p>

    <div class="flex mb-4 flex-col-reverse md:flex-row">
        <sub-idx-languages url-key="{{ $urlKey }}"></sub-idx-languages>

        <div class="lg:mx-auto md:ml-auto md:mb-0 mb-6">
            @include('helpers.ads.result-page-large-rectangle')
        </div>
    </div>


    <a class="tool-btn inline-block" href="{{ route('subIdx') }}">Back to tool</a>

@endsection
