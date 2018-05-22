<nav class="flex mb-4 py-1 px-4 border-b">
    <a class="mr-auto text-black" href="/">Subtitle Tools</a>

    <a class="mx-4 text-black" href="{{ route('admin') }}">{{ __('nav.item.admin') }}</a>
    <a class="mx-4 text-black" href="{{ route('adminFileJobs') }}">{{ __('nav.item.adminFileJobs') }}</a>
    <a class="mx-4 text-black" href="{{ route('admin.subIdx') }}">{{ __('nav.item.admin.subIdx') }}</a>
    <a class="mx-4 text-black" href="{{ route('admin.sup') }}">{{ __('nav.item.admin.sup') }}</a>

    <form class="ml-4" action="{{ route('logout') }}" method="POST" id="LogoutForm">
        {{ csrf_field() }}

        <a class="text-black" href="#" onclick="document.getElementById('LogoutForm').submit()">Logout</a>
    </form>
</nav>
