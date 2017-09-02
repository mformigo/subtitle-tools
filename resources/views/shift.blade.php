@extends('layout.base-template')

@section('title',       __('seo.title.shift'))
@section('description', __('seo.description.shift'))
@section('keywords',    __('seo.keywords.shift'))

@section('content')

    @component('components.page-intro')
        @slot('title') Resync Subtitles @endslot

        Online tool for resyncing subtitles by shifting all timestamps to make them appear earlier or later.
        If you want to resync multiple parts of a subtitle file, use the <a href="{{ route('shiftPartial') }}">Partial Shifter Tool</a>
    @endcomponent

    @component('components.tool-form')

        @slot('title') Select a file to shift @endslot

        @slot('formats') Supported subtitle formats: srt, ass, ssa, smi, zip @endslot

        @slot('extraAfter')
            <label for="ms-input" class="for-number">Shift (in milliseconds):</label>
            <input id="ms-input" placeholder="1000" type="number" name="milliseconds" value="{{ old('milliseconds') }}" required>
        @endslot

        @slot('buttonText') Shift @endslot

    @endcomponent

@endsection
