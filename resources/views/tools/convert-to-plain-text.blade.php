@extends('layout.base-template')

@section('title',       __('seo.title.convertToPlainText'))
@section('description', __('seo.description.convertToPlainText'))
@section('keywords',    __('seo.keywords.convertToPlainText'))

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Convert Subtitles to plain text</h1>
    <p>
        Online tool for extracting all text from subtitle files.
    </p>


    @component('components.tool-form')

        @slot('title') Select subtitles to convert to plain text @endslot

        @slot('formats') Supported subtitle formats: srt, ass, ssa, smi, sub, webvtt @endslot

        @slot('buttonText') Extract text @endslot

    @endcomponent


    <h2>Extracting text from subtitles</h2>
    <p>
        This tool extracts all text from subtitle files, it removes all timestamps and other effects.
        The output is saved as a text (.txt) file, this file can be opened by any text editor, such as Notepad or Microsoft Word.
        <br/><br/>
        This tool is especially useful for language learners, who can easily print out a transcript of a movie or video for studying.
        For Chinese students, it can be used together with the <a href="{{ route('pinyin') }}">pinyin subtitles tool</a> to create a plain text file with Chinese and pinyin.
        <br/><br/>
        You can upload multiple files at once. You can also upload a zip or rar file to convert a batch of subtitles.
    </p>

@endsection
