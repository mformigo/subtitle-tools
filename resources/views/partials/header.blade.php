<nav>
    <div class="nav-wrapper">
        <a href="/" class="brand-logo">Subtitle Tools</a>
        <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
        <ul class="right hide-on-med-and-down">
            <li {{ Request::is('shift*') ? 'class="active"' : '' }}>
                <a class="dropdown-button" href="#!" data-activates="dropdown1">
                    {{ __('nav.shifters_title') }}<i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>

            <ul id="dropdown1" class="dropdown-content">
                {!! nav_item('shift') !!}
                {!! nav_item('shift-partial') !!}
            </ul>

            {!! nav_item('convert-to-srt') !!}
            {!! nav_item('sub-idx-index') !!}
            {!! nav_item('clean-srt') !!}
            {!! nav_item('convert-to-utf8') !!}
            {!! nav_item('pinyin') !!}

        </ul>
        <ul class="side-nav" id="mobile-demo">
            <li class="hamburger-brand">Subtitle Tools</li>
            <li class="divider"></li>
            {!! nav_item('shift') !!}
            {!! nav_item('shift-partial') !!}
            {!! nav_item('convert-to-srt') !!}
            {!! nav_item('sub-idx-index') !!}
            {!! nav_item('clean-srt') !!}
            {!! nav_item('convert-to-utf8') !!}
            {!! nav_item('pinyin') !!}
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
