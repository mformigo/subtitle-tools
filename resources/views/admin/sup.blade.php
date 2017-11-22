@extends('admin.layout.admin-template')

@section('content')

<div class="container" id="SubIdxes">
    <h1>Sups</h1>

        <div class="st-row header">
            <div class="st-col minw-50"></div>
            <div class="st-col minw-100">Input</div>
            <div class="st-col minw-100">Output</div>
            <div class="st-col minw-100">Format</div>
            <div class="st-col minw-75">Cue Count</div>
            <div class="st-col minw-100">Ocr Lang</div>
            <div class="st-col st-grow">Size</div>
            <div class="st-col minw-75">Queue</div>
            <div class="st-col minw-75">Extract</div>
            <div class="st-col minw-75">Work</div>
            <div class="st-col minw-125">Age</div>
        </div>



        @foreach($sups as $sup)
            <div class="st-row">
                <div class="st-col minw-50"><a href="{{ route('sup.show', $sup->url_key) }}" target="_blank">&nbsp;(*)</a></div>
                <div class="st-col minw-100">{{ $sup->input_stored_file_id }}</div>
                <div class="st-col minw-100">
                    @if($sup->output_stored_file_id)
                        <a target="_blank" href="{{ route('adminStoredFileDetail', $sup->output_stored_file_id) }}">{{ $sup->output_stored_file_id }}</a>
                    @endif
                </div>
                <div class="st-col minw-100">{{ optional($sup->meta)->format }}</div>
                <div class="st-col minw-100">{{ optional($sup->meta)->cue_count }} cues</div>
                <div class="st-col minw-75">{{ $sup->ocr_language }}</div>
                <div class="st-col st-grow">{{ $sup->inputStoredFile->meta ? round($sup->inputStoredFile->meta->size / 1024, 0) : '' }}kb</div>
                <div class="st-col minw-75">{{ $sup->queue_time }} s</div>
                <div class="st-col minw-75">{{ $sup->extract_time }} s</div>
                <div class="st-col minw-75">{{ $sup->work_time }} s</div>
                <div class="st-col minw-125">{{ \Carbon\Carbon::parse($sup->created_at)->diffForHumans() }}</div>
            </div>

            <div class="st-row st-sub-row">
                <div class="st-col st-grow">{{ $sup->original_name }}</div>
            </div>
            <div class="st-row st-sub-row">
                <div class="st-col st-grow">{{ $sup->error_message }}</div>
            </div>
            <div class="st-row st-sub-row">
                <div class="st-col st-grow">{{ $sup->internal_error_message }}</div>
            </div>

        <br>
        <br>

        @endforeach

</div>

@endsection
