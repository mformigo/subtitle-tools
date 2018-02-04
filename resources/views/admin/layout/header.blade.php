<nav>
    <ul>
        <li>
            <a href="/">Subtitle Tools</a>
        </li>

        <li><a href="{{ route('admin') }}">{{ __('nav.item.admin') }}</a></li>
        <li><a href="{{ route('adminFileJobs') }}">{{ __('nav.item.adminFileJobs') }}</a></li>
        <li><a href="{{ route('admin.subIdx') }}">{{ __('nav.item.admin.subIdx') }}</a></li>
        <li><a href="{{ route('admin.sup') }}">{{ __('nav.item.admin.sup') }}</a></li>

        <form action="{{ route('logout') }}" method="POST" id="LogoutForm">
            {{ csrf_field() }}
            <li>
                <a href="#" onclick="document.getElementById('LogoutForm').submit()">Logout</a>
            </li>
        </form>
    </ul>
</nav>
