@extends('admin.layout.admin-template')

@section('content')

    <div class="flex">

        <div class="flex flex-col pl-8 w-64">
            <div>
                <strong class="block mb-2">Logs</strong>

                @foreach ($logs as $logName)
                    <div class="flex justify-between pl-4 border-l-8 border-red">
                        <a class="text-black" href="{{ route('adminGetLog', $logName) }}" target="_blank">{{ $logName }}</a>

                        <a class="text-black" href="{{ route('adminDeleteLog', $logName) }}">x</a>
                    </div>
                @endforeach
            </div>


            @if($failedJobCount > 0)
                <div>
                    <strong class="block mb-2 mt-8">Queues</strong>

                    <div class="flex justify-between pl-4 border-l-8 border-red">
                        <a class="text-black" href="{{ route('admin.failedJobs') }}">{{ $failedJobCount }} failed jobs</a>

                        <a class="text-black" href="{{ route('admin.failedJobs.truncate') }}">x</a>
                    </div>
                </div>
            @endif


            <div>
                <strong class="block mb-2 mt-8">Disk Usage</strong>

                <div class="pl-4 border-l-8 {{ $diskUsageWarning ? 'border-red' : 'border-green' }}">
                    {{ $diskUsage }}
                </div>
            </div>


            <div>
                <strong class="block mb-2 mt-8">Supervisor</strong>

                @foreach($supervisor as $superInfo)
                    <div class="flex items-center pl-4 border-l-8 {{ $superInfo->isRunning ? 'border-green' : 'border-red' }}">
                        <div class="w-48">{{ $superInfo->worker }}</div>
                        <small class="block ml-auto">{{ $superInfo->uptime }}</small>
                    </div>
                @endforeach
            </div>


            <div>
                <strong class="block mb-2 mt-8">Server</strong>

                <a class="block pl-4 text-black mb-2 border-l-8 border-grey-lighter" href="{{ route('admin.dashboard.phpinfo') }}" target="_blank">phpinfo()</a>

                @foreach($dependencies as $name => $isLoaded)
                    <div class="pl-4 border-l-8 {{ $isLoaded ? 'border-green' : 'border-red' }}">
                        {{ $name }}
                    </div>
                @endforeach
            </div>


            <div>
                <strong class="block mb-2 mt-8">Download Stored Files</strong>

                <form class="flex" target="_blank" method="post" action="{{ route('adminStoredFileDownload') }}">
                    {{ csrf_field() }}

                    <input type="text" name="id" class="field p-1 w-32" placeholder="stored file ids..." autocomplete="off" required />

                    <button type="submit" class="btn block p-1 ml-auto">Download</button>
                </form>
            </div>


            <div>
                <strong class="block mb-2 mt-8">Convert to Utf-8</strong>

                <form class="" target="_blank" method="post" enctype="multipart/form-data" action="{{ route('admin.ConvertToUtf8') }}">
                    {{ csrf_field() }}

                    <input type="file" name="file" class="p-1 w-full" required />

                    <input type="text" name="from_encoding" class="field p-1 w-full" placeholder="from encoding" autocomplete="on" required />

                    <button type="submit" class="btn block p-1 ml-auto">Convert</button>
                </form>
            </div>

        </div>


        @if($feedbackLines)
            <div class="w-128 ml-16">
                <h1>Feedback</h1>

                <div class="text-sm bg-white border rounded p-4">{!! implode("<br>", $feedbackLines) !!}</div>
            </div>
        @endif

    </div>

@endsection
