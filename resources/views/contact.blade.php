@extends('layout.base-template')

@section('title',       __('seo.title.contact'))
@section('description', __('seo.description.contact'))
@section('keywords',    __('seo.keywords.contact'))

@include('helpers.disconnect-echo')

@section('content')

    <h1>Contact</h1>
    <p>
        Your feedback is greatly appreciated!
    </p>


    <h2>Quick message</h2>
    <p>
        If you want to send me a quick message, type it here:
    </p>

    @if(isset($sentMessage))
        <div class="w-full md:w-1/2 mt-4 p-2 rounded bg-green-lighter border-l-2 border-green">
            Thank you for your message!
        </div>
    @endif

    @foreach ($errors->all() as $error)
        <div class="w-full md:w-1/2 mt-4 p-2 rounded bg-red-lighter border-l-2 border-red">
            {{ $error }}
        </div>
    @endforeach

    <form class="mt-4" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <textarea class="field block w-full md:w-1/2 h-24" name="message" placeholder="Your message here..." required></textarea>

        <button class="tool-btn">Send message</button>
    </form>


    <h2>Email</h2>
    <p>
        If you would like to ask a question, send me an email at: <a href="mailto:sfottjes@gmail.com">sfottjes@gmail.com</a>
        <br/><br/>
        If you have a problem with a subtitle file, please include it as an attachment.
    </p>

@endsection
