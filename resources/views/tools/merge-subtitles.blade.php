@extends('layout.base-template')

@section('title',       __('seo.title.mergeSubtitles'))
@section('description', __('seo.description.mergeSubtitles'))
@section('keywords',    __('seo.keywords.mergeSubtitles'))

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Merge Subtitles</h1>
    <p>
        Combine two subtitles into a single file.
    </p>


    @component('components.tool-form', ['bare' => true])

        @slot('title') Select subtitles to merge @endslot

        @slot('extraBefore')
            <strong class="block mb-2">Base file</strong>
            <input class="block" type="file" name="subtitles" required>
            <small class="block my-2">
                Supported formats: srt, ass, ssa, vtt
            </small>

            <strong class="block mt-4 mb-2">Merge file</strong>
            <input class="block" type="file" name="second-subtitle" required>
            <small class="block my-2">
                Supported formats: srt, ass, ssa, smi, sub, vtt
            </small>


            <strong class="block mt-4">Mode</strong>
            <div class="max-w-xs mt-2 ml-4 leading-normal">
                <label class="block cursor-pointer">
                    <input type="radio" name="mode" onchange="toggleThresholdField()" value="simple" {{ old('mode', 'simple') === 'simple' ? 'checked' : '' }}>
                    Simple
                </label>

                <label class="block cursor-pointer mt-3">
                    <input type="radio" name="mode" onchange="toggleThresholdField()" value="topBottom" {{ old('mode') === 'topBottom' ? 'checked' : '' }}>
                    Top and Bottom
                </label>

                <label class="block cursor-pointer my-3">
                    <input id="nearest-cue-mode" type="radio" name="mode" onchange="toggleThresholdField()" value="nearestCueThreshold" {{ old('mode') === 'nearestCueThreshold' ? 'checked' : '' }}>
                    Nearest cue
                </label>

                <label id="threshold-field" class="block cursor-pointer hidden" title="Nearest cue threshold in milliseconds">
                    Threshold (ms):
                    <input id="threshold-input" class="field py-1 w-24" type="number" min="1" name="nearest_cue_threshold" value="{{ old('nearest_cue_threshold', 1000) }}" required>
                </label>
            </div>
        @endslot

        @slot('buttonText') Merge @endslot

    @endcomponent


    <h2>About merging subtitles</h2>
    <p>
        Combining two subtitle files is usually used to create multilanguage subtitles.
        Multilanguage subtitles are useful when studying a new language.
        Merging subtitles can also be useful when your subtitles wrongly assume that the video has hard-coded subtitles for parts where a foreign language is spoken,
        you can then merge subtitles for the foreign language into the original subtitle file.
        <br><br>
        This tool requires two files, a base file and a merge file.
        The merge file will be merged into the base file.
        The format of the base file will not be changed.
    </p>

    <h3>Modes</h3>
    <p>
        This tool has multiple modes for merging subtitles, they are described below.
    </p>

    <h4>Simple merge</h4>
    <p>
        Using the simple mode, the merge file will simply be merged into the base file.
    </p>

    <h4>Top and Bottom merge</h4>
    <p>
        The top and bottom mode will merge the merge file into the base file and add style effects so the cues appear on top of the screen.
        Keep in mind that not all video players support showing subtitles on top of the screen.
    </p>

    <h4>Nearest cue merge</h4>
    <p>
        The nearest cue merge is similar to the simple merge, except that you can set a nearest cue threshold value.
        When merging cues from the merge file into the base file, the tool will look for nearby cues that are within the specified milliseconds threshold value.
        If there are nearby cues, the merge cue will be appended to the base file cue that is the most nearby.
        If there are no nearby cues, a new cue is created and added.
        <br><br>
        This mode is useful when you are combining two subtitles, and the cues from the base file appear slightly earlier or slightly later than the cues from the merge file.
    </p>


@endsection

@push('footer')
    <script>
        function toggleThresholdField() {
            var thresholdField = document.getElementById('threshold-field');

            if (document.getElementById('nearest-cue-mode').checked) {
                thresholdField.classList.remove('hidden');
            } else {
                thresholdField.classList.add('hidden');

                // prevent validation errors when the input is hidden.
                document.getElementById('threshold-input').value = 1000;
            }
        }

        toggleThresholdField();
    </script>
@endpush
