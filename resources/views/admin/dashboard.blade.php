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
                                Queues running: {{ count($supervisor) }}. That ain't right!
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
                    <div class="stored-file-download st-panel">
                        <h2>Stored Files</h2>

                        <form id="ToolForm" target="_blank" method="post" action="{{ route('adminStoredFileDownload') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group">
                            <label for="StoredFileId">Download ids:</label>
                            <input class="form-control" id="StoredFileId" type="text" name="id" autocomplete="off" required />
                            </div>

                            <button type="submit" class="float-right">Download</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-4">
                    <div class="st-panel">
                        <h2>Disk Usage</h2>

                        <div class="alert alert-{{ $diskUsageWarning ? 'danger' : 'success' }}">
                            {{ $diskUsage }}
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="not-found-panel st-panel">
                        <h2>404</h2>

                        @if(count($notFoundRequests) > 0)
                            <div class="log-controls">
                                @include('admin.components.form-url', ['route' => route('admin.dashboard.open404Log'),   'text' => 'Open log'])
                                @include('admin.components.form-url', ['route' => route('admin.dashboard.delete404Log'), 'text' => 'Delete log'])
                            </div>

                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($notFoundRequests as $notFound)
                                        <li>
                                            <span><strong>{{ $notFound['path'] }}</strong> {{ $notFound['count'] > 1 ? "({$notFound['count']}x)" : "" }}</span>
                                            <span>
                                                <form method="post" action="{{ route('admin.dashboard.append404Blacklist') }}" enctype="multipart/form-data">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="path" value="{{ $notFound['path'] }}">
                                                    <button type="submit" class="plain">Blacklist</button>
                                                </form>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                        @else
                            <div class="alert alert-success">
                                All good
                            </div>
                        @endif

                    </div>
                </div>

                <div class="col-4">
                    <div class="logs st-panel">
                        <h2>Queues</h2>

                        @if($failedJobCount > 0)
                            <div class="alert alert-danger">
                                <a href="{{ route('admin.failedJobs') }}">{{ $failedJobCount }} failed jobs</a>

                                <a href="{{ route('admin.failedJobs.truncate') }}">X</a>
                            </div>
                        @endif

                    </div>
                </div>

            </div>



            <div class="row">
                <div class="col-4">
                    <div class="st-panel">

                    </div>
                </div>

                <div class="col-4">
                    <div class="st-panel">

                    </div>
                </div>

                <div class="col-4">
                    <div class="st-panel">

                        <h2>Server</h2>

                        @foreach($phpVars as $name => $value)
                            {{ $value }} => {{ $name }} <br/>
                        @endforeach

                        <hr>

                        <a href="{{ route('admin.dashboard.phpinfo') }}" target="_blank">phpinfo()</a>

                        <hr>

                        @foreach($dependencies as $name => $isLoaded)
                            @if($isLoaded)
                                {{ $name }} <br/>
                            @else
                                <div class="alert alert-danger">
                                    {{ $name }}
                                </div>
                            @endif
                        @endforeach

                    </div>
                </div>

            </div>


        </div>
    </div>

@endsection
