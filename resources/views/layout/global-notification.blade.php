@if (now() < Carbon\Carbon::parse('2018-02-03'))
    <div id="global-notification" class="absolute pin-r mr-4 p-4 w-48 bg-green-lightest rounded hidden lg:block" aria-hidden="true">
        <strong class="block">New tool!</strong>
        You can now convert <a class="underline" href="{{ route('convertToVtt') }}">subtitles to WebVtt</a>

        <span class="absolute pin-t pin-r mr-2 cursor-pointer" onclick="document.getElementById('global-notification').outerHTML=''">✖</span>
    </div>
@endif
