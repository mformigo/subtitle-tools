@extends('admin.layout.admin-template')

@section('content')
<div class="ml-8">

    @isset($error)
        <div class="bg-red-lighter max-w-sm p-2 rounded mb-4">{{ $error }}</div>
    @endisset

    <div class="max-w-xs bg-white p-2 rounded border shadow mb-8">
        <strong class="block mb-2">Download Stored Files</strong>

        <form class="flex" target="_blank" method="post" action="{{ route('admin.storedFiles.download') }}">
            {{ csrf_field() }}

            <input type="text" name="id" class="field p-1 w-32" placeholder="stored file ids..." autocomplete="off" required />

            <button type="submit" class="btn block p-1 ml-auto">Download</button>
        </form>
    </div>


    <div class="max-w-xs bg-white p-2 rounded border shadow mb-8">
        <strong class="block mb-2">Delete Stored Files</strong>

        <form class="flex" method="post" action="{{ route('admin.storedFiles.delete') }}">
            {{ method_field('DELETE') }}
            {{ csrf_field() }}
            <input type="text" name="id" class="field p-1 w-32" placeholder="stored file ids..." autocomplete="off" required />

            <button type="submit" onclick="return confirm('Are you sure you want to delete this stored file?')" class="btn bg-red hover:bg-red-dark block p-1 ml-auto">Delete</button>
        </form>
    </div>


    <div class="max-w-xs bg-white p-2 rounded border shadow mb-8">
        <strong class="block mb-2">Convert to Utf-8</strong>

        <form class="" target="_blank" method="post" enctype="multipart/form-data" action="{{ route('admin.convertToUtf8') }}">
            {{ csrf_field() }}

            <input type="file" name="file" class="p-1 w-full" required />

            <input type="text" name="from_encoding" class="field p-1 w-full" placeholder="from encoding" autocomplete="on" required />

            <button type="submit" class="btn block p-1 ml-auto">Convert</button>
        </form>
    </div>


</div>
@endsection
