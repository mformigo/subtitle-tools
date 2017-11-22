@extends('admin.layout.admin-template')

@section('content')

<div class="container" id="SubIdxes">
    <h1>Failed Jobs</h1>

        <div class="st-row header">
            <div class="st-col minw-75">Connection</div>
            <div class="st-col minw-75">Queue</div>
            <div class="st-col minw-125">Time Ago</div>
        </div>


        @foreach($failedJobs as $failedJob)
            <div class="st-row">
                <div class="st-col minw-75">{{ $failedJob->connection }}</div>
                <div class="st-col minw-75">{{ $failedJob->queue }}</div>
                <div class="st-col minw-125">{{ \Carbon\Carbon::parse($failedJob->failed_at)->diffForHumans() }}</div>
            </div>

            <strong>Payload</strong>
            <pre>
{{ $failedJob->payload }}
            </pre>

            <strong>Exception</strong>
            <pre>
{{ $failedJob->exception }}
            </pre>

        @endforeach

</div>

@endsection
