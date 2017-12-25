@extends('admin.layout.admin-template')

@section('content')

    <div id="showLog">
        <div class="container">
            <h1>{{ $name }}</h1>
            <br/>

            @php
                $inStacktrace = false;
                $entriesCounter = 0;
            @endphp

            @foreach($lines as $line)

                @if($inStacktrace)
                    {{ $line }}<br/>

                    @if($line === '"} ')
                        @php $inStacktrace = false; @endphp
                        </div>
                    @endif

                @elseif(starts_with($line, '['))

                    @if($line === '[stacktrace]')
                        @php
                            $inStacktrace = true;
                        @endphp
                        <div class="log-stacktrace hidden" id="LogEntry{{ $entriesCounter++ }}">
                    @else
                        @php
                            list($timeStamp, $message) = explode('] ', substr($line, 1), 2);
                        @endphp

                        <hr/>

                        <span class="toggle-stacktrace-button" onclick="toggleStacktrace({{ $entriesCounter }})">+</span>

                        <strong>{{ \Carbon\Carbon::parse($timeStamp)->diffForHumans() }}</strong> &nbsp;&nbsp;&nbsp; {{ $timeStamp }}<br/>
                        {{ $message }} <br/>

                        <br/>
                    @endif

                @endif

            @endforeach

        </div>
    </div>

@endsection

@push('footer')
    <script>
        function toggleStacktrace(entryNumber)
        {
            var el = document.getElementById('LogEntry'+entryNumber);

            el.classList.toggle('hidden');
        }
    </script>
@endpush
