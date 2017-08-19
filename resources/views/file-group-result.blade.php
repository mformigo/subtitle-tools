@extends('base-template')

@section('content')

    <h1>Single Download</h1>

    Group has: {{ $fileCount }} FileJobs

    <br/>
    <br/>

    <div id="GroupResult">

        <file-group-jobs url-key="{{ $urlKey }}"></file-group-jobs>

        <file-group-archive url-key="{{ $urlKey }}"></file-group-archive>

    </div>

    <br/>
    <br/>

    <a href="{{ $returnUrl }}">{{ $returnUrl }}</a>

@endsection