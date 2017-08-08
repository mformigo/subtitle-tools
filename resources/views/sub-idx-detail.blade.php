@extends('base-template')

@section('content')

    <h1>Sub Idx Detail</h1>

    <p>
        Extracting srt files from <strong>{{ $originalName }}</strong>
    </p>


    <sub-idx-languages page-id="{{ $pageId }}"></sub-idx-languages>

@endsection