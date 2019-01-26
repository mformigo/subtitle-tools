@extends('admin.layout.admin-template')

@section('content')

<h1>Sub Idx</h1>

<div class="flex max-w-2xl bg-white rounded shadow border p-4 mb-8">
    <div class="flex-grow">
        <div class="font-semibold mb-2">Sub/idx cache hit leaderboards</div>

        @foreach($subIdxCacheHitList as $subIdx)
            <div class="flex text-sm mb-1">
                <div class="font-semibold mr-2">{{ $subIdx->cache_hits }}x</div>
                <input type="text" class="bg-white w-full" value="{{ $subIdx->original_name }}" readonly>
            </div>
        @endforeach
    </div>

    <div class="ml-16 text-center">
        <h2 class="m-0">Files in queue</h2>
        <div class="text-4xl mt-4">{{ $filesInQueue }}</div>
    </div>
</div>


@foreach($subIdxes as $subIdx)
    <div class="pb-4 mb-4 border-b text-sm">
        <a href="{{ route('subIdx.show', $subIdx->url_key) }}" target="_blank">{{ $subIdx->original_name }}</a>
        <div class="text-xs my-1">
            <span class="font-semibold">{{ $subIdx->created_at }}</span>
            ({{ \Carbon\Carbon::parse($subIdx->created_at)->diffForHumans() }})
        </div>
        <div class="text-xs">
            sub: {{ format_file_size($subIdx->sub_file_size) }}
            <span class="mx-2"></span>
            idx: {{ format_file_size($subIdx->idx_file_size) }}
        </div>

        <div class="bg-grey-lightest text-xs ml-8 mt-2 p-2">
            <div class="flex font-bold mb-2">
                <div class="w-16"></div>
                <div class="w-32">Output stored file</div>
                <div class="w-1/6">Queued at</div>
                <div class="w-1/6">Time in queue</div>
                <div class="w-1/6">Started at</div>
                <div class="w-1/6">Finished at</div>
                <div class="w-1/6">Times downloaded</div>
            </div>

            @foreach($subIdx->languages as $lang)
                <div class="flex {{ $loop->last ? '' : 'mb-1 pb-1 border-b' }}">
                    <div class="w-16">{{ $lang->language }}</div>
                    <div class="w-32">
                        @if($lang->output_stored_file_id)
                        <a target="_blank" href="{{ route('admin.storedFiles.show', $lang->output_stored_file_id) }}">{{ $lang->output_stored_file_id }}</a>
                        @endif
                    </div>
                    <div class="w-1/6">{{ $lang->queued_at ? $lang->queued_at : '' }}</div>
                    <div class="w-1/6">{{ $lang->started_at ? $lang->started_at->diffForHumans($lang->queued_at) : '' }}</div>
                    <div class="w-1/6">{{ $lang->started_at ? $lang->started_at : '' }}</div>
                    <div class="w-1/6">{{ $lang->finished_at ? $lang->finished_at : '' }}</div>
                    <div class="w-1/6">
                        {{ $lang->error_message ? $lang->error_message : '' }}
                        {{ $lang->times_downloaded ? $lang->times_downloaded.'x' : '' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach


@endsection
