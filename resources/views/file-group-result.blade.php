@extends('base-template')

@section('content')

    <h1>Single Download</h1>

    Group has: {{ $fileCount }} FileJobs

    <br/>
    <br/>

    <file-group-result url-key="{{ $urlKey }}"></file-group-result>

    <br/>
    <br/>

    <a href="{{ $returnUrl }}">{{ $returnUrl }}</a>

@endsection