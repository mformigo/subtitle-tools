@extends('layout.base-template')

@section('title',       __('seo.title.convertToVtt'))
@section('description', __('seo.description.convertToVtt'))
@section('keywords',    __('seo.keywords.convertToVtt'))

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Convert Subtitles to Vtt</h1>
    <p>
        Online tool for changing subtitles to webvtt
        <br/><br/>
        You can select multiple files or upload an archive file to convert a batch of subtitles at once
    </p>


    @component('components.tool-form')

        @slot('title') Select files to convert to vtt @endslot

        @slot('formats') Supported subtitle formats: srt, ass, ssa, smi, sub @endslot

        @slot('buttonText') Convert to Vtt @endslot

        @slot('extraBefore')
            <div id="sub-idx-popup" class="block mb-4 p-3 bg-yellow-lighter max-w-sm hidden">
                For .sub + .idx files, use the <a class="font-bold" href="{{ route('subIdx') }}">sub/idx converter</a>.
            </div>

            <div id="sup-popup" class="block mb-4 p-3 bg-yellow-lighter max-w-sm hidden">
                For .sup files, use the <a class="font-bold" href="{{ route('sup') }}">sup converter</a>.
            </div>
        @endslot

    @endcomponent

    @push('footer')
        <script>
            $('#subtitles-input').on("change", function() {
                var selectedFileName = $('#subtitles-input').val().split('\\').pop();

                var isSubIdxFile = selectedFileName.match(/\.(sub|idx)$/i);
                var isSupFile    = selectedFileName.match(/\.sup$/i);

                $("#sub-idx-popup").toggleClass("hidden", !isSubIdxFile);

                $("#sup-popup").toggleClass("hidden", !isSupFile);
            });
        </script>
    @endpush


    <h2>About converting to WebVtt</h2>
    <p>
        <a href="https://developer.mozilla.org/en-US/docs/Web/API/WebVTT_API" rel="nofollow" target="_blank">Web Video Text Tracks Format (WebVTT)</a> is a modern subtitle format used for online video subtitles.
        This online tool converts many text-based subtitle formats to vtt.
    </p>

@endsection
