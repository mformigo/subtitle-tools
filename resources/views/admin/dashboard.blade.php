@extends('admin.layout.admin-template')

@section('content')
    <div class="flex mb-4 p-4 border rounded shadow bg-white">

        <div class="border-l-8 pl-2 {{ $diskUsage->warning ? ' border-red' : 'border-green' }} mr-16">
            <div class="flex items-center justify-between font-semibold mb-2">
                Disk usage
                <a class="text-xs text-black" href="{{ route('admin.diskUsage.index') }}">details</a>
            </div>

            <div class="text-lg">
                {{ $diskUsage->error ? $diskUsage->error : "$diskUsage->used / $diskUsage->size ($diskUsage->percentage)" }}
            </div>
        </div>


        <div class="border-l-8 pl-2 {{ $dependencies->reject(true)->isEmpty() ? ' border-green' : 'border-red' }} mr-16">
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



    @if($feedbackLines)
        <form id="feedback-log" class="mb-8" data-times-clicked="0" method="post" action="{{ route('admin.feedback.delete') }}">
            {{ csrf_field() }}
            {{ method_field('delete') }}

            <div class="flex items-center mb-2">
                <h4 class="mt-0 mr-16">feedback</h4>
                <div class="text-xs text-red-light font-semibold cursor-pointer select-none" onclick="submitWhenClickedOften('feedback-log')">delete</div>
            </div>
            <pre class="text-xs max-w-md overflow-scroll max-h-96 p-4 border-2 bg-grey-lightest">{!! implode('', $feedbackLines) !!}</pre>
        </form>
    @endif



    @if($errorLogLines)
        <form id="error-log" class="mb-8" data-times-clicked="0" method="post" action="{{ route('admin.errorLog.delete') }}">
            {{ csrf_field() }}
            {{ method_field('delete') }}

            <div class="flex items-center mb-2">
                <h4 class="mt-0 mr-16">laravel.log</h4>
                <div class="text-xs text-red-light font-semibold cursor-pointer select-none" onclick="submitWhenClickedOften('error-log')">delete</div>
            </div>
            <pre class="text-xs max-w-3xl overflow-scroll max-h-96 p-4 border-2 bg-grey-lightest">{{ implode("\r\n", str_replace(base_path(), '', $errorLogLines)) }}</pre>
        </form>
    @endif



    @if($failedJobs->isNotEmpty())
        <form id="failed-jobs" class="mb-8" data-times-clicked="0" method="post" action="{{ route('admin.failedJobs.truncate') }}">
            {{ csrf_field() }}
            {{ method_field('delete') }}

            <div class="flex items-center mb-2">
                <h4 class="mt-0 mr-16">failed jobs</h4>
                <div class="text-xs text-red-light font-semibold cursor-pointer select-none" onclick="submitWhenClickedOften('failed-jobs')">delete</div>
            </div>
<pre class="text-xs max-w-3xl overflow-scroll max-h-96 p-4 border-2 bg-grey-lightest">
@foreach($failedJobs as $failedJob)
<strong>Queue:</strong> {{ $failedJob->queue }}
<strong>Failed at:</strong> {{ $failedJob->failed_at }}
<strong>Payload</strong>
{{ $failedJob->payload }}

<strong>Exception</strong>
{{ str_replace(base_path(), '', $failedJob->exception) }}

-----

@endforeach
</pre>
        </form>
    @endif



@endsection


@push('footer')
    <script>
        function submitWhenClickedOften(id)
        {
            var el = document.getElementById(id);

            if (++el.dataset.timesClicked >= 5) {
                el.submit();
            }
        }
    </script>
@endpush
