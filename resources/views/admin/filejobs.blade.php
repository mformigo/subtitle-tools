@extends('admin.layout.admin-template')

@section('content')

    <div class="max-w-4xl text-sm pl-4 mb-16">
        <h1>File Jobs</h1>

        <div class="flex px-2 font-bold">
            <div class="w-3/12">Original Name</div>
            <div class="w-2/12">Error Message</div>
            <div class="w-2/12">Encoding</div>
            <div class="w-1/12">Type</div>
            <div class="w-2/12">Files</div>
            <div class="w-2/12">Finished at</div>
        </div>

        @foreach($fileJobs as $fileJob)
            <div class="flex px-2 py-1 border-t hover:bg-grey-light">
                <div class="w-3/12">
                    <input type="text" class="w-11/12 bg-grey-lighter" value="{{ $fileJob->original_name }}" readonly>
                </div>
                <div class="w-2/12">
                    {{ __($fileJob->error_message) }}
                </div>
                <div class="w-2/12">
                    {{ optional($fileJob->inputStoredFile->meta)->encoding }}
                </div>
                <div class="w-1/12">
                    {{ substr(optional($fileJob->inputStoredFile->meta)->identified_as, strlen('App\Subtitles\PlainText\\')) }}
                </div>
                <div class="w-2/12">
                    <a target="_blank" href="{{ route('adminStoredFileDetail', $fileJob->input_stored_file_id) }}">{{ $fileJob->input_stored_file_id }}</a>
                    <span class="mx-2">🡆</span>
                    @if($fileJob->output_stored_file_id)
                        <a target="_blank" href="{{ route('adminStoredFileDetail', $fileJob->output_stored_file_id) }}">{{ $fileJob->output_stored_file_id }}</a>
                    @endif
                </div>
                <div class="w-2/12">
                    {{ $fileJob->finished_at }}
                </div>
            </div>
        @endforeach
    </div>

@endsection
