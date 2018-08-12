@extends('layout.base-template')

@section('title',       __('seo.title.contact'))
@section('description', __('seo.description.contact'))
@section('keywords',    __('seo.keywords.contact'))

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Contact</h1>
    <p class="max-w-sm">
        If you want to give feedback, or simply send me a message, you can do that using the form below.
        <br><br>
        I try to reply to all messages within three days.
        Please don't hesitate to contact me, i like hearing from my users.
    </p>


    <h2>Quick message</h2>
    <p class="max-w-sm">
        You can use this form to send me a quick message.
        If you would like a response, don't forget to include your email.
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

    <form class="mt-4 w-full max-w-sm p-2" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <input class="field block w-full mb-2" type="text" name="email" value="{{ old('email') }}" placeholder="Email (optional)">

        <textarea class="field block w-full h-24" name="message" placeholder="Your message here..." required>{{ old('message') }}</textarea>

        <div class="flex items-center mt-2">
            <div id="hard-math" class="mr-4">3</div>

            <input type="text" class="field w-10" name="captcha" placeholder="?" required>

            <button class="tool-btn block my-0 ml-auto">Send message</button>
        </div>
    </form>


    <h2>Email</h2>
    <p class="max-w-sm">
        If you would like me to take a look at one of your subtitle files, you can send me an email at: <a href="mailto:sfottjes@gmail.com">sfottjes@gmail.com</a>.
        You can include the file as an attachment.
    </p>

@endsection

@push('footer')
    <script>
        setTimeout(function () {
            var el = document.getElementById('hard-math');

            el.innerText += ' + 2 =';
        }, 1000)
    </script>
@endpush
