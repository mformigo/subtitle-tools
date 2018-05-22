@extends('admin.layout.admin-template')

@section('content')

    <div class="flex flex-col pl-8 w-96">
        <div>
            <strong class="block mb-2">Logs</strong>

            @foreach ($logs as $logName)
                <div class="flex justify-between pl-4 border-l-8 border-red">
                    <a class="text-black" href="{{ route('adminGetLog', $logName) }}" target="_blank">{{ $logName }}</a>

                    <a class="text-black" href="{{ route('adminDeleteLog', $logName) }}">x</a>
                </div>
            @endforeach
        </div>


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
                    <small class="block ml-auto">{{ $superInfo->uptime }} uptime</small>
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
            <strong class="block mb-2 mt-8">Download Stored Files</strong>

            <form class="flex" target="_blank" method="post" action="{{ route('adminStoredFileDownload') }}">
                {{ csrf_field() }}

                <input type="text" name="id" class="field p-1" placeholder="stored file ids..." autocomplete="off" required />

                <button type="submit" class="btn block p-1 ml-auto">Download</button>
            </form>
        </div>


        <div>
            <strong class="block mb-2 mt-8">Server</strong>

            <a class="block pl-4 text-black mb-2 border-l-8 border-grey-lighter" href="{{ route('admin.dashboard.phpinfo') }}" target="_blank">phpinfo()</a>

            @foreach($phpVars as $name => $value)
                <div class="pl-4 border-l-8 border-grey-lighter">{{ $value }} {{ $name }}</div>
            @endforeach

            <div class="mb-2"></div>

            @foreach($dependencies as $name => $isLoaded)
                <div class="pl-4 border-l-8 {{ $isLoaded ? 'border-green' : 'border-red' }}">
                    {{ $name }}
                </div>
            @endforeach
        </div>

    </div>

@endsection
