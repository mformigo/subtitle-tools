@extends('guest.layout.base-template')

@section('title',       __('seo.title.cleanSrt'))
@section('description', __('seo.description.cleanSrt'))
@section('keywords',    __('seo.keywords.cleanSrt'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Srt Cleaner @endslot

        This tool cleans up srt files, removing html effects contained in angle brackets/chevrons, such as &lt;b&gt;&lt;/b&gt;.
        It also removes effects left over when poorly converted from the different subtitle formats, like {\f4}, contained in curly brackets/braces.
        The file will be converted to UTF-8, and the cues will be sorted based on their start time, and duplicate or empty cues will be removed.

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


@endsection
