@extends('guest.layout.base-template')

@section('title',       __('seo.title.contact'))
@section('description', __('seo.description.contact'))
@section('keywords',    __('seo.keywords.contact'))

@include('helpers.disconnect-echo')

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Contact @endslot

        Your feedback is greatly appreciated!

    @endcomponent

    <div class="container">
        <section class="quick-message">

            <h2>Quick message</h2>
            <p>
                If you want to send me a quick message, type it here:
            </p>

            <form method="post" enctype="multipart/form-data">
                {{ csrf_field() }}

                <textarea name="message" placeholder="Your message here..." required></textarea>

                <button type="submit" class="btn">
                    <i class="material-icons right">send</i>
                    Send Message
                </button>

                @if(session('sentMessage'))
                    <div class="alert alert-success">
                        <strong>Thank you for your feedback!</strong>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </form>

        </section>
    </div>

    <div class="container">
        <section class="contact-email">
            <h2>Email</h2>
            <p>
                If you would like to ask a question, send me an email at: <a class="fw-b" href="mailto:sfottjes@gmail.com">sfottjes@gmail.com</a>
                <br/><br/>
                If you have a problem with a subtitle file, please include it as an attachment.
            </p>

        </section>
    </div>

@endsection
