@extends('layout.base-template')

@section('title',       __('seo.title.mergeSubtitles'))
@section('description', __('seo.description.mergeSubtitles'))
@section('keywords',    __('seo.keywords.mergeSubtitles'))

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Merge Subtitles</h1>
    <p>
        Merge two subtitles into a single file.
    </p>


    @component('components.tool-form', ['bare' => true])

        @slot('title') Select subtitles to merge @endslot

        @slot('extraBefore')
            <strong class="block mb-2">First file</strong>
            <input class="block" type="file" name="subtitles" required>

            <strong class="block mt-4 mb-2">Second file</strong>
            <input class="block" type="file" name="second-subtitle" required>

            <small class="block my-2">
                Supported formats: srt, ass, ssa, smi, sub, vtt
            </small>


            <strong>Mode</strong>
            <div class="max-w-xs leading-normal">
                <label class="block cursor-pointer">
                    <input type="radio" name="mode" value="simple" {{ old('mode', 'simple') === 'simple' ? 'checked' : '' }}>
                    Simple
                </label>

                <label class="block cursor-pointer my-3">
                    <input type="radio" name="mode" value="nearestCueThreshold" {{ old('mode') === 'nearestCueThreshold' ? 'checked' : '' }}>
                    Nearest cue
                </label>

                <label class="block cursor-pointer">
                    Threshold:
                    <input class="field" type="number" min="1" name="nearest_cue_threshold" value="{{ old('nearest_cue_threshold', 1000) }}" title="Nearest cue threshold in milliseconds" required>
                </label>


            </div>


        @endslot

        @slot('buttonText') Merge @endslot

    @endcomponent


    <h2>About merging subtitles</h2>
    <p>

    </p>

@endsection
