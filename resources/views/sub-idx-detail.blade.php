@extends('base-template')

@section('content')

    <h1>Sub Idx Detail</h1>

    @foreach($languages as $lang)
        {{ $lang }}<br/>
    @endforeach

@endsection