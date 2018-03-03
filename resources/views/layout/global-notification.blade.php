@if (now() < Carbon\Carbon::parse('2018-03-05'))
    <div id="global-notification" class="absolute pin-r mr-4 p-4 w-48 bg-green-lightest rounded hidden lg:block" aria-hidden="true">
        <strong class="block">Update!</strong>
        The <a href="{{ route('cleanSrt') }}"> srt cleaner tool</a> can now strip [square brackets]!

        <span class="absolute pin-t pin-r mr-2 cursor-pointer" onclick="document.getElementById('global-notification').outerHTML=''">✖</span>
    </div>
@endif
