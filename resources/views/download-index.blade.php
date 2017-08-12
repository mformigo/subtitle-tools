@extends('base-template')

@section('content')

    <h1>Download</h1>

    {{ $originalName }}

    <br/>
    <br/>

    <a href="{{ $returnUrl }}">{{ $returnUrl }}</a>

@endsection