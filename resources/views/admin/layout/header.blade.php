<nav class="flex mb-4 py-1 px-4 border-b">
    <a class="mr-auto text-black" href="/">Subtitle Tools</a>

    <a class="mx-4 text-black" href="{{ route('admin') }}">Dashboard</a>
    <a class="mx-4 text-black" href="{{ route('admin.tools.index') }}">Tools</a>
    <a class="mx-4 text-black" href="{{ route('adminFileJobs') }}">File jobs</a>
    <a class="mx-4 text-black" href="{{ route('admin.subIdx') }}">Sub/idx</a>
    <a class="mx-4 text-black" href="{{ route('admin.sup') }}">Sup</a>

    <form class="ml-4" action="{{ route('logout') }}" method="POST" id="LogoutForm">
        {{ csrf_field() }}

        <a class="text-black" href="#" onclick="document.getElementById('LogoutForm').submit()">Logout</a>
    </form>
</nav>
