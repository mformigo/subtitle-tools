@extends('guest.layout.base-template')

@section('title',       __('seo.title.convertToSrt'))
@section('description', __('seo.description.convertToSrt'))
@section('keywords',    __('seo.keywords.convertToSrt'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Convert Subtitles to Srt @endslot

        Online tool for changing subtitles to srt.
        You can select multiple files or upload a zip or rar file to convert a batch of subtitles to srt.

    @endcomponent


    @component('guest.components.tool-form')

        @slot('title') Select subtitles to convert to srt @endslot

        @slot('formats') Supported subtitle formats: ass, ssa, smi, microdvd, zip, rar @endslot

        @slot('buttonText') Convert to Srt @endslot

        @slot('extraBefore')
            <div class="alert-danger sub-idx-popup hidden">
                If you have a .sub file and an .idx file then you need to use the <a href="{{ route('subIdx') }}">sub/idx converter</a>.
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

    <section class="text-content">
        <div class="container">

            <h2>About converting to subrip</h2>
            <p>
                Subrip (srt) is a very basic subtitle format, because of this you will almost always lose some functionality or effects when converting to srt.
                This online format converter tool works with Windows, Mac (Apple) and Linux and doesn't require you to install freeware on your computer.
                The paragraphs below describe what you can expect when converting your subtitles to srt.
                You can learn more about the subrip format <a href="https://matroska.org/technical/specs/subtitles/srt.html" rel="nofollow" target="_blank">here</a>.
            </p>

            <h3>Converting ass to srt</h3>
            <p>
                Advanced Substation Alpha (ass) is, as the name says, a more advanced version of the Substation Alpha (ssa) format.
                It supports many effects, a few examples are custom fonts, pictures, positioned text, colors, moving text and karaoke text.
                Srt doesn't support any of these things, and when converting ass to srt, all these effects are either removed or changed to normal text.
                Changing ass files to srt files usually works pretty well, except for the occasional overlapping text as a result of removing text position effects.
                You can learn more about the ssa and ass format on the <a href="https://en.wikipedia.org/wiki/SubStation_Alpha" rel="nofollow" target="_blank">Wikipedia page</a>.
            </p>

            <h3>Converting smi to srt</h3>
            <p>
                Synchronized Accessible Media Interchange (sami or smi) is an old subtitle format originally <a href="https://msdn.microsoft.com/en-us/library/ms971327.aspx" rel="nofollow" target="_blank">created by Microsoft</a>.
                Smi files are barely ever used these days because there are far superior alternatives like srt or ass.
                Korea used to use the smi format to create subtitles for movies, most old Korean movies that come with subtitles use the smi format.
                Smi files support multiple languages in the same subtitle file, which should work fine when converting to srt.
            </p>

            <h3>Converting MicroDVD (sub) to srt</h3>
            <p>
                <a href="https://en.wikipedia.org/wiki/MicroDVD" target="_blank" rel="nofollow">MicroDVD</a> subtitle files are weird, but for some reason still common.
                The dialogue inside a MicroDVD file is timed based on the frame rate of the video.
                When converting sub to srt, we need to know the frame rate.
                Some sub files have a fps hint as the first cue, if this hint is present we use this fps to determine the timing of the dialogue.
                If no hint is present, we assume 23.976 fps.
                <strong>If your .sub file is accompanied by an .idx file, you need to use the <a href="{{ route('subIdx') }}">sub/idx converter</a>.</strong>
            </p>

            <h3>Polish MPL2 to srt</h3>
            <p>
                The subtitle format MPL2 is also supported, it is commonly used to make Polish subtitles.
                The program SubEdit is used to make these mpl subtitles.
            </p>

            <h3>Converting a batch of subtitles</h3>
            <p>
                You can convert up to a hundred files at the same time by uploading multiple files.
                You can also upload a zip files or rar (winrar) files.
                The tool will attempt to convert all the files inside the archive file.
                After uploading you will be redirected to the download page, where you can individually download the converted files, or generate a zip file and download them all at once.
            </p>

        </div>
    </section>

@endsection
