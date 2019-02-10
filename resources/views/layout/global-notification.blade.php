@if (now() < Carbon\Carbon::parse('2019-02-25'))
    <div id="global-notification" class="absolute pin-r mr-4 p-4 w-48 bg-green-lightest rounded hidden lg:block" aria-hidden="true">

        Subtitle Tools is now <strong>open source</strong> and hosted on <a class="font-bold underline" href="https://github.com/SjorsO/subtitle-tools">Github!</a>

        <span class="absolute pin-t pin-r mr-2 cursor-pointer" onclick="document.getElementById('global-notification').outerHTML=''">✖</span>
    </div>
@endif
