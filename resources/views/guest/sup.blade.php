@extends('guest.layout.base-template')

@section('title',       __('seo.title.sup'))
@section('description', __('seo.description.sup'))
@section('keywords',    __('seo.keywords.sup'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Sup to Srt @endslot

        This tool converts subpicture files to srt

    @endcomponent


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

            <h2>Cleaning srt files</h2>
            <p>

            </p>

            <h3>Other formatting tags</h3>
            <p>

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