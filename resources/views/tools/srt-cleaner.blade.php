@extends('layout.base-template')

@section('title',       __('seo.title.cleanSrt'))
@section('description', __('seo.description.cleanSrt'))
@section('keywords',    __('seo.keywords.cleanSrt'))

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Remove formatting from Srt subtitles</h1>
    <p>
        Cleans srt files by stripping html tags and other formatting
    </p>


    @component('components.tool-form')

        @slot('title') Select files to clean @endslot

        @slot('formats') Supported subtitle formats: srt @endslot

        @slot('buttonText') Clean @endslot

        @slot('extraAfter')
            {{-- Hidden inputs so the values are always present in the request --}}
            <input type="hidden" name="stripParentheses" value="" />
            <input type="hidden" name="stripCurly"       value="" />
            <input type="hidden" name="stripAngle"       value="" />
            <input type="hidden" name="stripSquare"      value="" />

            <label class="block">
                <input type="checkbox" name="stripParentheses" value="1" {{ old('stripParentheses', 'checked') }}>
                Strip text between parentheses ( )
            </label>

            <label class="block my-3">
                <input type="checkbox" name="stripCurly" value="1" {{ old('stripCurly', 'checked') }}>
                Strip text between curly brackets { }
            </label>

            <label class="block my-3">
                <input type="checkbox" name="stripAngle" value="1" {{ old('stripAngle', 'checked') }}>
                Strip text between angle brackets &lt; &gt;
            </label>

            <label class="block mb-3">
                <input type="checkbox" name="stripSquare" value="1" {{ old('stripSquare', 'checked') }}>
                Strip text between square brackets [ ]
            </label>
        @endslot

    @endcomponent


    <h2>Cleaning srt files</h2>
    <p>
        Srt subtitles sometimes contain style formatting tags. Unfortunately, many video players don't support formatting and display them as plain text.
        Examples of formatting are italic {{ '<i></i>' }}, bold {{ '<b></b>' }} or colored text {{ '<font></font>' }}.
        This tool strips all html formatting that is contained in angle brackets.
        <br/><br/>
        The cleaner also converts the file to UTF-8 text encoding, the cues will be sorted based on their start time, and duplicate or empty cues will be removed.
    </p>

    <h3>Cleaning hearing-impaired subtitles</h3>
    <p>
        The <i>strip text between parentheses</i> option can be used to turn subtitles for the deaf and hard-of-hearing (SDH subtitles) into regular subtitles.
        This option will remove any SDH text (which should be between parentheses), leaving only dialogue cues.
        If the SDH text is contained in different kinds of brackets, you can use one of the other options to strip them too.
    </p>

    <h3>Other formatting tags</h3>
    <p>
        This tool also removes all effects that are leftover when converting a subtitle format to srt.
        Most notably, it removes formatting effects contained in curly brackets (eg: {\f4}) which come from substation alpha subtitles.
        The <a href="{{ route('convertToSrt') }}">srt converter</a> tool will properly remove these effects when converting to srt, but many other tools available online do not.
    </p>

@endsection
