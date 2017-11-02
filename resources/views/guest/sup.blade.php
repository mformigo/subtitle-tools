@extends('guest.layout.base-template')

@section('title',       __('seo.title.sup'))
@section('description', __('seo.description.sup'))
@section('keywords',    __('seo.keywords.sup'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Sup to Srt @endslot

        Online sup to srt converter

    @endcomponent

    <div class="container">
        <div class="alert alert-warning" role="alert">
            <strong>This tool is still in beta!</strong>
            It might not work correctly, please send me <a class="feedback" title="{{ __('nav.item.contact') }}" href="{{ route('contact') }}">feedback</a>.
        </div>
    </div>

    @component('guest.components.tool-form', ['singleFile' => true])

        @slot('title') Select sup to convert to srt @endslot

        @slot('buttonText') Convert @endslot

        @slot('filePlaceholder') Select sup file... @endslot

        @slot('extraAfter')

            <div class="input-field m-b-32">

                <select name="ocrLanguage" id="ocrLanguage">
                    @foreach($languages as $language)
                        <option value="{{ $language }}">{{ __('languages.tesseract.'.$language) }}</option>
                    @endforeach
                </select>
                <label>Select the sup language</label>

            </div>

        @endslot

    @endcomponent


    <section class="text-content">
        <div class="container">

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

        </div>
    </section>

@endsection

@push('inline-footer-scripts')
    <script>
        $(document).ready(function() {
            $('select').material_select();
        });
    </script>
@endpush