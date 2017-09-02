<nav>
    <ul>
        {!! nav_item('admin') !!}
        {!! nav_item('adminFileJobs') !!}

        <form action="{{ route('logout') }}" method="POST" id="LogoutForm">
            {{ csrf_field() }}
            <li>
                <a href="#" onclick="document.getElementById('LogoutForm').submit()">Logout</a>
            </li>
        </form>
    </ul>
</nav>
