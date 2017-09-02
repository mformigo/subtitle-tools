@extends('admin.layout.admin-template')

@section('content')

    <div id="Dashboard">
        <div class="container">
            <h1>Dashboard</h1>

            <div class="row">
                <div class="col-4">
                    <div class="logs st-panel">
                        <h2>Logs</h2>
                        @forelse ($logs as $logName)
                            <div class="alert alert-danger">
                                <a href="{{ route('adminGetLog', ['name' => $logName]) }}" target="_blank">{{ $logName }}</a>

                                <a href="{{ route('adminDeleteLog', ['name' => $logName]) }}">X</a>
                            </div>
                        @empty
                            <div class="alert alert-success">
                                All good
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="col-4">
                    <div class="supervisor st-panel">
                        <h2>Supervisor</h2>
                        @if(!$goodSupervisor)
                            <div class="alert alert-danger">
                                Only {{ count($supervisor) }} queues are running!
                            </div>
                        @endif

                        @foreach($supervisor as $superInfo)
                            <div class="alert alert-{{ $superInfo->isRunning ? 'success' : 'danger' }}">
                                <strong>{{ $superInfo->worker }}</strong> uptime: {{ $superInfo->uptime }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-4">

                </div>
            </div>


        </div>
    </div>

@endsection
