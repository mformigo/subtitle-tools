@extends('admin.layout.admin-template')

@section('content')

    <h2 class="mb-4">Disk Usage</h2>

    <div class="flex border-b py-2 font-bold">
        <div class="w-48"></div>
        <div class="w-32">Total size</div>
        <div class="w-24">Total used</div>
        <div class="w-16"></div>
        <div class="w-32">Stored Files</div>
        <div class="w-32">Sub/Idx</div>
        <div class="w-32">Temp dirs</div>
        <div class="w-32">Temp files</div>
    </div>

    @foreach($diskUsages as $diskUsage)
        <div class="flex border-b py-2 hover:bg-grey-light">
            <div class="w-48">{{ $diskUsage->created_at->format('Y-m-d H:i') }}</div>
            <div class="w-32">{{ format_file_size($diskUsage->total_size) }}</div>
            <div class="w-24">{{ format_file_size($diskUsage->total_used) }}</div>
            <div class="w-16">{{ $diskUsage->total_usage_percentage }}%</div>
            <div class="w-32">{{ format_file_size($diskUsage->stored_files_dir_size) }}</div>
            <div class="w-32">{{ format_file_size($diskUsage->sub_idx_dir_size) }}</div>
            <div class="w-32">{{ format_file_size($diskUsage->temp_dirs_dir_size) }}</div>
            <div class="w-32">{{ format_file_size($diskUsage->temp_files_dir_size) }}</div>
        </div>
    @endforeach

@endsection
