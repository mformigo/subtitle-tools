@extends('base-template')

@section('content')

    <h1>Download</h1>

    Group has: {{ $fileCount }} FileJobs

    <br/>
    <br/>

    <a href="{{ $returnUrl }}">{{ $returnUrl }}</a>

@endsection