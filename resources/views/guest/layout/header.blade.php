<nav>
    <div class="nav-wrapper">
        <a href="{{ route('home') }}" class="brand-logo">Subtitle Tools</a>
        <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
        <ul class="right hide-on-med-and-down">

            <li class="{{ Request::routeIs('shift*') ? "active" : '' }}">
                <a class="dropdown-button" href="#!" data-activates="dropdown1">
                    {{ __('nav.shifters_title') }}<i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>
            <ul id="dropdown1" class="dropdown-content">
                {!! nav_item('shift') !!}
                {!! nav_item('shiftPartial') !!}
            </ul>

            {!! nav_item('convertToSrt') !!}
            {!! nav_item('subIdx') !!}
            {!! nav_item('sup') !!}

            <li class="{{ Request::routeIs('pinyin*') || Request::routeIs('convertToPlainText*') ? "active" : '' }}">
                <a class="dropdown-button" href="#!" data-activates="dropdown2">
                    {{ __('nav.other_title') }}<i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>
            <ul id="dropdown2" class="dropdown-content">
                {!! nav_item('pinyin') !!}
                {!! nav_item('convertToPlainText') !!}
                {!! nav_item('convertToUtf8') !!}
                {!! nav_item('cleanSrt') !!}
            </ul>
            <li>
                <a class="patreon-header-link" target="_blank" href="https://www.patreon.com/subtitletools">
                    Become a Patron!
                </a>
            </li>

        </ul>

        <ul class="side-nav" id="mobile-demo">
            <li class="hamburger-brand">Subtitle Tools</li>
            <li class="divider"></li>
            {!! nav_item('shift') !!}
            {!! nav_item('shiftPartial') !!}
            {!! nav_item('convertToSrt') !!}
            {!! nav_item('subIdx') !!}
            {!! nav_item('sup') !!}
            {!! nav_item('cleanSrt') !!}
            {!! nav_item('convertToUtf8') !!}
            {!! nav_item('pinyin') !!}
            {!! nav_item('convertToPlainText') !!}
            <li class="divider"></li>
            <li>
                <a target="_blank" href="https://www.patreon.com/subtitletools">
                    Become a Patron!
                </a>
            </li>
            <li class="divider"></li>
            {!! nav_item('contact') !!}
        </ul>
    </div>
</nav>

@push('inline-footer-scripts')
    <script>
        $(document).ready(function() {
            $(".dropdown-button").dropdown();

            $(".button-collapse").sideNav();
        });
    </script>
@endpush
