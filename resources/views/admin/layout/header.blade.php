<div class="p-4 border-t border-b {{ Route::is('admin.dashboard.*') ? 'bg-grey-lighter font-semibold' : '' }}">
    <a class="text-black" href="{{ route('admin.dashboard.index') }}">Dashboard</a>
</div>

<div class="p-4 border-b {{ Route::is('admin.tools.*') ? 'bg-grey-lighter font-semibold' : '' }}">
    <a class="text-black" href="{{ route('admin.tools.index') }}">Tools</a>
</div>

<div class="p-4 border-b {{ Route::is('admin.fileJobs.*') ? 'bg-grey-lighter font-semibold' : '' }}">
    <a class="text-black" href="{{ route('admin.fileJobs.index') }}">File jobs</a>
</div>

<div class="p-4 border-b {{ Route::is('admin.subIdx.*') ? 'bg-grey-lighter font-semibold' : '' }}">
    <a class="text-black" href="{{ route('admin.subIdx.index') }}">Sub/idx</a>
</div>

<div class="p-4 border-b {{ Route::is('admin.sup.*') ? 'bg-grey-lighter font-semibold' : '' }}">
    <a class="text-black" href="{{ route('admin.sup.index') }}">Sup</a>
</div>

<form class="flex absolute pin-b mb-2 ml-4" action="{{ route('logout') }}" method="post">
    {{ csrf_field() }}

    <button class="w-4 h-4">
        @include('helpers.svg.logout')
    </button>

    <a class="block w-4 h-4 ml-8 font-semibold text-black" href="/">
        @include('helpers.svg.home')
    </a>
</form>
