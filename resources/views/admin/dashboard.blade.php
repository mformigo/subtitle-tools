@extends('admin.layout.admin-template')

@section('content')

    <div class="flex mb-4 p-4 border rounded shadow bg-white">

        <div class="border-l-8 pl-2 {{ $diskUsageWarning ? ' border-red' : 'border-green' }} mr-16">
            <div class="flex items-center justify-between font-semibold mb-2">
                Disk usage
                <a class="text-xs text-black" href="{{ route('admin.diskUsage.index') }}">details</a>
            </div>

            <div class="text-lg">{{ $diskUsage }}</div>
        </div>


        <div class="border-l-8 pl-2 {{ $dependencies->filter()->count() === $dependencies->count() ? ' border-green' : 'border-red' }} mr-16">
            <div class="flex items-center justify-between font-semibold mb-2">
                Server
                <a class="text-xs text-black" href="{{ route('admin.showPhpinfo') }}">phpinfo()</a>
            </div>

            @if($dependencies->reject(true)->isEmpty())
                Dependencies are OK
            @else
                @foreach($dependencies as $name => $isLoaded)
                <div class="text-xs pl-2 border-l-4 {{ $isLoaded ? 'border-green' : 'border-red' }}">{{ $name }}</div>
                @endforeach
            @endif
        </div>


        <div class="border-l-8 pl-2 {{ $supervisor->every->isRunning ? ' border-green' : 'border-red' }} mr-16">
            <div class="font-semibold mb-2">Queues</div>

            @if($supervisor->every->isRunning)
                {{ count($supervisor) }} workers running
            @else
                @foreach($supervisor as $superInfo)
                    <div class="text-xs pl-2 border-l-8 {{ $superInfo->isRunning ? 'border-green' : 'border-red' }}">{{ $superInfo->worker }}</div>
                @endforeach
            @endif
        </div>

    </div>


    <div class="flex">

        <div class="flex flex-col w-64">
            <div>
                <strong class="block mb-2">Logs</strong>

                @foreach ($logs as $logName)
                    <div class="flex justify-between pl-4 border-l-8 border-red">
                        <a class="text-black" href="{{ route('admin.logs.show', $logName) }}" target="_blank">{{ $logName }}</a>

                        <a class="text-black" href="{{ route('admin.logs.delete', $logName) }}">x</a>
                    </div>
                @endforeach
            </div>

            @if($failedJobCount > 0)
                <div>
                    <strong class="block mb-2 mt-8">Queues</strong>

                    <div class="flex justify-between pl-4 border-l-8 border-red">
                        <a class="text-black" href="{{ route('admin.failedJobs.index') }}">{{ $failedJobCount }} failed jobs</a>

                        <a class="text-black" href="{{ route('admin.failedJobs.truncate') }}">x</a>
                    </div>
                </div>
            @endif
        </div>


        @if($feedbackLines)
            <div class="w-128 ml-16">
                <h1>Feedback</h1>

                <div class="text-sm bg-white border rounded p-4">{!! implode("<br>", $feedbackLines) !!}</div>
            </div>
        @endif

    </div>
@endsection
