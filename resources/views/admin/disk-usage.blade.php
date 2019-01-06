@extends('admin.layout.admin-template')

@section('content')
<div class="ml-8">

    <h2 class="">Disk Usage</h2>
    <p class="text-sm mb-8">
        A <code class="mx-2 font-semibold">du -h --total</code> of the <code class="mx-2 font-semibold">/storage/app/</code> directory.
    </p>

<pre class="text-sm">
@foreach($dirs as $name => $subDirs)
<strong>{{ $name }}</strong>
@foreach($subDirs as $subDir)
{{ $subDir }}
@endforeach

@endforeach
</pre>

    <div class="mb-16"></div>

</div>
@endsection
