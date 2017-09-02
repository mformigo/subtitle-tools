@extends('layout.base-template')

@section('title',       __('seo.title.convertToSrt'))
@section('description', __('seo.description.convertToSrt'))
@section('keywords',    __('seo.keywords.convertToSrt'))

@section('content')

    @component('components.page-intro')

        @slot('title') Convert Subtitles to Srt @endslot

        Online tool for changing subtitles to srt.
        It automatically detects file encoding and encodes the output in UTF-8.
        Supports the following conversions: <strong>ass to srt</strong>, <strong>ssa to srt</strong> and <strong>smi to srt</strong>.
        You can select multiple files or upload a zip file to convert a batch of subtitles to srt.

    @endcomponent


    @component('components.tool-form')

        @slot('title') Select subtitles to convert to srt @endslot

        @slot('formats') Supported subtitle formats: ass, ssa, smi, zip @endslot

        @slot('buttonText') Convert to Srt @endslot

        @slot('extraBefore')
            <div class="alert-danger sub-idx-popup hidden">
                It looks like you're trying to convert a sub/idx file.
                You need to use the <a href="{{ route('subIdx') }}">sub/idx converter</a> for that.
            </div>
        @endslot

    @endcomponent

    @push('inline-footer-scripts')
        <script>
            $('#SubtitlesInput').on("change", function() {
                var selectedFileName = $('#SubtitlesInput').val().split('\\').pop();

                var isSubIdxFile = selectedFileName.match(/\.(sub|idx)$/i);

                $(".sub-idx-popup").toggleClass("hidden", !isSubIdxFile);
            });
        </script>
    @endpush

    @component('components.text-section')

        @component('components.text-section-content', ['h2' => true])
            @slot('title') About converting to subrip @endslot

            Subrip (srt) is a very basic subtitle format, because of this you will almost always lose some functionality or effects when converting to srt.
            This online format converter tool works with Windows, Mac (Apple) and Linux and doesn't require you to install freeware on your computer.
            The paragraphs below describe what you can expect when converting your subtitles to srt.
            You can learn more about the subrip format <a href="https://matroska.org/technical/specs/subtitles/srt.html" rel="nofollow" target="_blank">here</a>.
        @endcomponent

        @component('components.text-section-content')
            @slot('title') Converting ass to srt @endslot

            Advanced Substation Alpha (ass) is, as the name says, a more advanced version of the Substation Alpha (ssa) format.
            It supports many effects, a few examples are custom fonts, pictures, positioned text, colors, moving text and karaoke text.
            Srt doesn't support any of these things, and when converting ass to srt, all these effects are either removed or changed to normal text.
            Changing ass files to srt files usually works pretty well, except for the occasional overlapping text as a result of removing text position effects.
            You can learn more about the ssa and ass format on the <a href="https://en.wikipedia.org/wiki/SubStation_Alpha" rel="nofollow" target="_blank">Wikipedia page</a>.
        @endcomponent

        @component('components.text-section-content')
            @slot('title') Converting smi to srt @endslot

            Synchronized Accessible Media Interchange (sami or smi) is an old subtitle format originally <a href="https://msdn.microsoft.com/en-us/library/ms971327.aspx" rel="nofollow" target="_blank">created by Microsoft</a>.
            Smi files are barely ever used these days because there are far superior alternatives like srt or ass.
            Korea used to use the smi format to create subtitles for movies, most old Korean movies that come with subtitles use the smi format.
            Smi files support multiple languages in the same subtitle file, which should work fine when converting to srt.
        @endcomponent

        @component('components.text-section-content')
            @slot('title') Converting a batch of subtitles using zip @endslot

            You can convert up to a hundred files at the same time with this tool by uploading a zip file, or by simply selecting multiple files.
            The tool will attempt to convert all the files inside the zip file.
            After uploading you will be redirected to the download page, where you can individually download the converted files, or generate a zip file and download them all at once.
        @endcomponent

    @endcomponent

@endsection
