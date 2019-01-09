<nav class="flex mb-4 py-1 px-4 border-b">
    <a class="mr-auto text-black" href="/">Subtitle Tools</a>

    <a class="mx-4 text-black" href="{{ route('admin.dashboard.index') }}">Dashboard</a>
    <a class="mx-4 text-black" href="{{ route('admin.tools.index') }}">Tools</a>
    <a class="mx-4 text-black" href="{{ route('admin.fileJobs.index') }}">File jobs</a>
    <a class="mx-4 text-black" href="{{ route('admin.subIdx.index') }}">Sub/idx</a>
    <a class="mx-4 text-black" href="{{ route('admin.sup.index') }}">Sup</a>

    <form class="ml-4" action="{{ route('logout') }}" method="POST" id="LogoutForm">
        {{ csrf_field() }}

        <a class="text-black" href="#" onclick="document.getElementById('LogoutForm').submit()">Logout</a>
    </form>
</nav>
