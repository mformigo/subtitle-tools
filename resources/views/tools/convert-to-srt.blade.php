@extends('layout.base-template')

@section('title',       __('seo.title.convertToSrt'))
@section('description', __('seo.description.convertToSrt'))
@section('keywords',    __('seo.keywords.convertToSrt'))

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Convert Subtitles to Srt</h1>
    <p>
        Online tool for changing subtitles to srt
        <br/><br/>
        You can select multiple files or upload a zip or rar file to convert a batch of subtitles to srt
    </p>


    @component('components.tool-form')

        @slot('title') Select files to convert to srt @endslot

        @slot('formats') Supported subtitle formats: ass, ssa, smi, sub, vtt @endslot

        @slot('buttonText') Convert to Srt @endslot

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



    <h2>About converting to subrip</h2>
    <p>
        Subrip (srt) is a very basic subtitle format, because of this you will almost always lose some functionality or effects when converting to srt.
        This free online format converter works with Windows, Mac (Apple) and Linux and doesn't require you to install freeware on your computer.
        The paragraphs below describe what you can expect when converting your subtitles to srt.
        You can learn more about the subrip format <a href="https://matroska.org/technical/specs/subtitles/srt.html" rel="nofollow" target="_blank">here</a>.
    </p>

    <h3>Converting ssa/ass to srt</h3>
    <p>
        Advanced Substation Alpha (ass) is, as the name says, a more advanced version of the Substation Alpha (ssa) format.
        It supports many effects, a few examples are custom fonts, pictures, positioned text, colors, moving text and karaoke text.
        Srt doesn't support any of these things, and when converting ass to srt, all these effects are either removed or changed to normal text.
        Changing ass files to srt files usually works pretty well, except for the occasional overlapping text as a result of removing text position effects.
        You can learn more about the ssa and ass format on the <a href="https://en.wikipedia.org/wiki/SubStation_Alpha" rel="nofollow" target="_blank">Wikipedia page</a>.
    </p>

    <h3>Converting WebVTT to srt</h3>
    <p>
        <a href="https://developer.mozilla.org/en-US/docs/Web/API/WebVTT_API" rel="nofollow" target="_blank">Web Video Text Tracks Format (WebVTT)</a> is a modern subtitle format used for online video subtitles.
        It is similar to the srt format in many ways. It differs in being more customizable.
        WebVTT supports styling on text, positioning and karaoke effects.
        Since these effects are not supported by srt, they are stripped when converting vtt to srt.
        <br/><br/>
        WebVTT files use the .vtt file extension and are a plain text subtitle format.
        The first line of a WebVTT file should start with WEBVTT.
        This is how the format is identified.
        If the file does not start with this tag, converting it will probably fail, or result in incorrect output.
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

    <h3>oTranscribe to srt</h3>
    <p>
        Transcripts made with <a href="http://otranscribe.com" rel="nofollow" target="_blank">oTranscribe</a> are supported.
        If you export your transcript as <i>plain text (.txt)</i>, you can use this tool to convert it to a subtitle file.
        Make sure you don't export your transcript as a markdown file, the bold and italic effects will not be converted correctly.
        If you would like markdown transcripts to be supported, <a href="{{ route('contact') }}">send me a message.</a>
    </p>

    <h3>Converting a batch of subtitles</h3>
    <p>
        You can convert up to a hundred files at the same time by uploading multiple files.
        You can also upload a zip files or rar (winrar) files.
        The tool will attempt to convert all the files inside the archive file.
        After uploading you will be redirected to the download page, where you can individually download the converted files, or generate a zip file and download them all at once.
    </p>


@endsection
