@extends('admin.layout.admin-template')

@section('content')
    <h1>Sups</h1>

    <div class="max-w-md bg-white rounded shadow border p-2 mb-8">
        <div class="font-semibold mb-2">Sup cache hit leaderboards</div>

        @foreach($supCacheHitList as $sup)
            <div class="flex text-sm mb-1">
                <div class="font-semibold mr-2">{{ $sup->cache_hits }}x</div>
                <input type="text" class="bg-white w-full" value="{{ $sup->original_name }}" readonly>
            </div>
        @endforeach
    </div>


    @foreach($sups as $sup)
    <div class="flex hover:bg-grey-light mb-8 font-mono text-sm border-l-4 pl-4 {{ $sup->is_finished ? ($sup->has_error ? 'border-red' : 'border-green') : 'border-yellow-dark' }}">
        <div class="mr-24">
            <a href="{{ route('sup.show', $sup->url_key) }}" target="_blank">Result page</a>

            <div><span class="font-bold">Input stored file id:</span> {{ $sup->input_stored_file_id }}</div>

            <div>
                <span class="font-bold">Output stored file id:</span>
                @if($sup->output_stored_file_id)
                    <a target="_blank" href="{{ route('admin.storedFiles.show', $sup->output_stored_file_id) }}">{{ $sup->output_stored_file_id }}</a>
                @else
                    none
                @endif
            </div>

            <div><span class="font-bold">Queue time:</span> {{ $sup->queue_time }} s</div>
            <div><span class="font-bold">Extract time:</span> {{ $sup->extract_time }} s</div>
            <div><span class="font-bold">Work time:</span> {{ $sup->work_time }} s</div>
            <div><span class="font-bold">Created at:</span> {{ \Carbon\Carbon::parse($sup->created_at)->diffForHumans() }}</div>
        </div>

        <div>

            <div><span class="font-bold">Sup format:</span> {{ $sup->meta ? $sup->meta->format : 'no meta yet' }}</div>
            <div><span class="font-bold">Cue count:</span> {{ $sup->meta ? $sup->meta->cue_count : 'no meta yet' }}</div>
            <div><span class="font-bold">Sup size:</span> {{ ($sup->inputStoredFile->meta ?? null) ? format_file_size($sup->inputStoredFile->meta->size) : 'no input stored file meta' }}</div>
            <div><span class="font-bold">Ocr language:</span> {{ $sup->ocr_language }}</div>
            <div><span class="font-bold">Error message:</span> {{ $sup->error_message }}</div>
            <div><span class="font-bold">Internal error message:</span> {{ $sup->internal_error_message }}</div>
            <div><span class="font-bold">Original name:</span> {{ $sup->original_name }}</div>
        </div>
    </div>
    @endforeach

@endsection
