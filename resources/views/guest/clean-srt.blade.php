@extends('guest.layout.base-template')

@section('title',       __('seo.title.cleanSrt'))
@section('description', __('seo.description.cleanSrt'))
@section('keywords',    __('seo.keywords.cleanSrt'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Remove formatting from Srt subtitles @endslot

        This tool cleans srt files, removing html tags and other formatting.

    @endcomponent


    @component('guest.components.tool-form')

        @slot('title') Select subtitles to clean @endslot

        @slot('formats') Supported subtitle formats: srt @endslot

        @slot('buttonText') Clean @endslot

        @slot('extraAfter')

            <div class="options-group checkboxes">

                <input type="hidden"   name="stripCurly" value="" />
                <input type="checkbox" name="stripCurly" value="checked" id="stripCurly" class="filled-in" {{ old('stripCurly', 'checked') }}>
                <label for="stripCurly">Strip curly brackets { }</label>

                <input type="hidden"   name="stripAngle" value="" />
                <input type="checkbox" name="stripAngle" value="checked" id="stripAngle" class="filled-in" {{ old('stripAngle', 'checked') }}>
                <label for="stripAngle">Strip angle brackets &lt; &gt;</label>

            </div>

        @endslot

    @endcomponent


    <section class="text-content">
        <div class="container">

            <h2>Cleaning srt files</h2>
            <p>
                Srt subtitles sometimes contain style formatting tags. Unfortunately, many video players don't support formatting and display them as plain text.
                Examples of formatting are italic {{ '<i></i>' }}, bold {{ '<b></b>' }} or colored text {{ '<font></font>' }}.
                This tool strips all html formatting that is contained in angle brackets.
                <br/><br/>
                The cleaner also converts the file to UTF-8 text encoding, the cues will be sorted based on their start time, and duplicate or empty cues will be removed.
            </p>

            <h3>Other formatting tags</h3>
            <p>
                This tool also removes all effects that are leftover when converting a subtitle format to srt.
                Most notably, it removes formatting effects contained in curly brackets (eg: {\f4}) which come from substation alpha subtitles.
                The <a href="{{ route('convertToSrt') }}">srt converter</a> tool will properly remove these effects when converting to srt, but many other tools available online do not.
            </p>

        </div>
    </section>

@endsection
