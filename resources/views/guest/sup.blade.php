@extends('guest.layout.base-template')

@section('title',       __('seo.title.sup'))
@section('description', __('seo.description.sup'))
@section('keywords',    __('seo.keywords.sup'))

@include('helpers.disconnect-echo')

@section('content')

    <h1>Sup to Srt</h1>
    <p>
        Online sup to srt converter
    </p>

    <div class="inline-block mt-8 p-3 bg-yellow-lighter">
        <strong class="block mb-1">This tool is still in beta!</strong>
        It might not work correctly yet
    </div>

    @component('guest.components.tool-form', ['singleFile' => true])

        @slot('title') Select sup to convert to srt @endslot

        @slot('buttonText') Convert @endslot

        @slot('extraAfter')
            <label class="block font-bold">
                Select the sup language
                <select name="ocrLanguage" class="block shadow border rounded py-1 px-2 mt-2">
                    @foreach($languages as $language)
                        <option value="{{ $language }}">{{ __('languages.tesseract.'.$language) }}</option>
                    @endforeach
                </select>
            </label>
        @endslot

    @endcomponent


    <h2>About sup files</h2>
    <p>
        Sup files are subtitle files that contain pictures of text.
        Televisions can show these pictures and not have to worry about text encoding or fonts.
        The downside to this is that the text in the pictures can't be changed, and that computers usually can't display the subtitles.
    </p>

    <h3>Subtitle language for OCR</h3>
    <p>
        This tool uses <a href="https://en.wikipedia.org/wiki/Tesseract_(software)" rel="nofollow" target="_blank">Tesseract OCR</a> to read the text in the pictures, and converts the file to srt.
        You need to select the language of your sup file for Tesseract to work correctly.
        If you're not sure what language your sup file is, just give English a try and see if it works.
    </p>

    <h3>Sup formats</h3>
    <p>
        This tool supports three different sup formats: bluray, hddvd and normal dvd.
        The code for reading these formats is <a href="https://github.com/SjorsO/sup">open source</a>, you can help improve it.
    </p>

@endsection
