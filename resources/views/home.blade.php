@extends('base-template')

@section('title',       __('seo.title.'))
@section('description', __('seo.description.'))
@section('keywords',    __('seo.keywords.'))

@section('content')

    <h1>Subtitle Tools</h1>

    <br/>

    <a href="{{ route('convertToSrt') }}">Convert to Srt</a>
    <br/>
    <a href="{{ route('cleanSrt') }}">Clean Srt</a>
    <br/>
    <a href="{{ route('subIdx') }}">Sub/Idx to Srt</a>
    <br/>
    <a href="{{ route('shift') }}">Shift</a>
    <br/>
    <a href="{{ route('shiftPartial') }}">Shift Partial</a>
    <br/>
    <a href="{{ route('convertToUtf8') }}">Convert to Utf8</a>
    <br/>
    <a href="{{ route('pinyin') }}">Pinyin subs</a>

@endsection