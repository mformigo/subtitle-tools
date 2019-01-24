@extends('admin.layout.admin-template')

@section('content')

<div class="container" id="SubIdxes">
    <h1>Sub Idx</h1>

    <div class="w-96 bg-white rounded shadow border mr-8 p-4">
        <div class="font-semibold mb-2">Sub/idx cache hit leaderboards</div>

        @foreach($subIdxCacheHitList as $subIdx)
            <div class="flex text-sm mb-1">
                <div class="font-semibold mr-2">{{ $subIdx->cache_hits }}x</div>
                <input type="text" class="bg-white w-full" value="{{ $subIdx->original_name }}" readonly>
            </div>
        @endforeach
    </div>



        <div class="st-row header">
            <div class="st-col minw-75">Page</div>
            <div class="st-col st-grow">Original Name</div>
            <div class="st-col minw-75">Sub Size</div>
            <div class="st-col minw-75">Idx Size</div>
            <div class="st-col minw-125">Age</div>
        </div>


        @foreach($subIdxes as $subIdx)
            <div class="st-row">
                <div class="st-col minw-75"><a href="{{ route('subIdx.show', $subIdx->url_key) }}" target="_blank">&nbsp;(*)</a></div>
                <div class="st-col st-grow">{{ $subIdx->original_name }}</div>
                <div class="st-col minw-75">{{ $subIdx->meta ? round($subIdx->meta->sub_file_size / 1024, 2) : '' }}kb</div>
                <div class="st-col minw-75">{{ $subIdx->meta ? round($subIdx->meta->idx_file_size / 1024, 2) : '' }}kb</div>
                <div class="st-col minw-125">{{ \Carbon\Carbon::parse($subIdx->created_at)->diffForHumans() }}</div>
            </div>


            <div class="st-row st-sub-row header">
                <div class="st-col minw-100">Lang</div>
                <div class="st-col minw-100">Output</div>
                <div class="st-col st-grow">Error</div>
                <div class="st-col minw-75">Queue</div>
                <div class="st-col minw-75">Extract</div>
                <div class="st-col minw-75">Timeout</div>
            </div>

            @foreach($subIdx->languages as $lang)
                <div class="st-row st-sub-row">
                    <div class="st-col minw-100">{{ $lang->language }}</div>
                    <div class="st-col minw-100">
                        @if($lang->output_stored_file_id)
                            <a target="_blank" href="{{ route('admin.storedFiles.show', $lang->output_stored_file_id) }}">{{ $lang->output_stored_file_id }}</a>
                        @endif
                    </div>
                    <div class="st-col st-grow">{{ $lang->error_message }}</div>
                    <div class="st-col minw-75">{{ $lang->queue_time }} s</div>
                    <div class="st-col minw-75">{{ $lang->extract_time }} s</div>
                    <div class="st-col minw-75">((old timed-out column))</div>
                </div>
            @endforeach

        @endforeach

</div>

@endsection
