@extends('base-template')

@section('title',       __('seo.title.'))
@section('description', __('seo.description.'))
@section('keywords',    __('seo.keywords.'))

@section('content')

    <h1>Subtitle Tools</h1>

    <br/>

    <a href="{{ route('convert-to-srt') }}">Convert to Srt</a>
    <br/>
    <a href="{{ route('clean-srt') }}">Clean Srt</a>
    <br/>
    <a href="{{ route('sub-idx-index') }}">Sub/Idx to Srt</a>
    <br/>
    <a href="{{ route('shift') }}">Shift</a>
    <br/>
    <a href="{{ route('shift-partial') }}">Shift Partial</a>
    <br/>
    <a href="{{ route('convert-to-utf8') }}">Convert to Utf8</a>
    <br/>
    <a href="{{ route('pinyin') }}">Pinyin subs</a>

@endsection