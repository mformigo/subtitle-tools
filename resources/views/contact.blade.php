@extends('layout.base-template')

@section('title',       __('seo.title.contact'))
@section('description', __('seo.description.contact'))
@section('keywords',    __('seo.keywords.contact'))

@section('content')

    @component('components.page-intro')

        @slot('title') Contact @endslot

        Your feedback is greatly appreciated.
        If you have any ideas for improvements, new tools, or if you found a mistake in one of the current tools, please don't hesitate to send us a message.
        If one of the tools doesn't work properly with your subtitle file, please include the subtitle file as an attachment.
        <br/>
        <br/>
        You can contact us at the following email address: <a class="fw-b" href="mailto:sfottjes@gmail.com">sfottjes@gmail.com</a>

    @endcomponent

@endsection
