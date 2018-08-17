@extends('admin.layout.admin-template')

@section('content')

    <div class="font-mono overflow-x-scroll text-xs mx-4">
        <h1>{{ $name }}</h1>


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
                    </div>
                @endif

            @elseif(starts_with($line, '['))

                @if($line === '[stacktrace]')
                    @php
                        $inStacktrace = true;
                    @endphp
                    <div class="hidden overflow-x-scroll whitespace-no-wrap" id="LogEntry{{ $entriesCounter++ }}">
                @else
                    @php
                        list($timeStamp, $message) = explode('] ', substr($line, 1), 2);
                    @endphp

                    <div class="mb-8">

                    <span class="cursor-pointer" onclick="toggleStacktrace({{ $entriesCounter }})">+</span>

                    <strong>{{ \Carbon\Carbon::parse($timeStamp)->diffForHumans() }}</strong> &nbsp;&nbsp;&nbsp; {{ $timeStamp }}<br/>
                    <input type="text" class="w-full bg-grey-lighter" value="{{ $message }}" readonly>

                    <br/>
                @endif

            @endif

        @endforeach

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
