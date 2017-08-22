@extends('base-template')

@section('title',       __('seo.title.'))
@section('description', __('seo.description.'))
@section('keywords',    __('seo.keywords.'))

@section('content')

    <h1>Sub Idx Detail</h1>

    <p>
        Extracting srt files from <strong>{{ $originalName }}</strong>
    </p>


    <sub-idx-languages page-id="{{ $pageId }}"></sub-idx-languages>

@endsection