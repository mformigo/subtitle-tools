<nav class="mb-4 p-3 bg-red-light">
    <div class="container mx-auto flex">
        <a class="text-white text-xl" href="{{ route('home') }}">Subtitle Tools</a>

        <div class="ml-auto flex">
            <div class="hidden md:flex">
                <a class="mx-3 text-white" href="{{ route('convertToSrt') }}">
                    {{ __('nav.item.convertToSrt') }}
                </a>

                <a class="mx-3 text-white" href="{{ route('convertToUtf8') }}">
                    {{ __('nav.item.convertToUtf8') }}
                </a>

                <a class="mx-3 text-white" href="{{ route('cleanSrt') }}">
                    {{ __('nav.item.cleanSrt') }}
                </a>
            </div>

            <a class="mx-3 text-white flex items-center" id="hamburger-opener" href="javascript:" onclick="toggleHamburgerMenu()">
                <span class="mr-3 text-xl">☰</span>
                All Tools
            </a>
        </div>



        <div id="hamburger-menu" class="bg-grey-lighter z-50">
            <div id="hamburger-closer" onclick="closeHamburgerMenu()">
                ✖
            </div>

            <div class="flex flex-col text-right">
                <h3 class="border-b pb-3 my-3 pr-3">Tools</h3>

                <strong class="my-2 pr-3">Syncing</strong>
                {!! hamburger_item('shift') !!}
                {!! hamburger_item('shiftPartial') !!}

                <strong class="my-2 pr-3 border-t pt-2">Converting</strong>
                {!! hamburger_item('convertToSrt') !!}
                {!! hamburger_item('convertToVtt') !!}
                {!! hamburger_item('subIdx') !!}
                {!! hamburger_item('sup') !!}
                {!! hamburger_item('convertToPlainText') !!}

                <strong class="my-2 pr-3 border-t pt-2">Fixing</strong>
                {!! hamburger_item('cleanSrt') !!}
                {!! hamburger_item('convertToUtf8') !!}

                <strong class="my-2 pr-3 border-t pt-2">Other</strong>
                {!! hamburger_item('pinyin') !!}
                {!! hamburger_item('contact') !!}
            </div>
        </div>



    </div>

</nav>


@push('footer')
    <script>
        var hamburgerMenu = document.getElementById('hamburger-menu');

        function toggleHamburgerMenu() {
            hamburgerMenu.classList.toggle('hamburger-visible');
        }

        function closeHamburgerMenu() {
            hamburgerMenu.classList.remove('hamburger-visible');
        }
    </script>
@endpush
