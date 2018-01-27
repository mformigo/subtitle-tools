@extends('layout.base-template')

@section('title',       __('seo.title.home'))
@section('description', __('seo.description.home'))
@section('keywords',    __('seo.keywords.home'))

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Subtitle Tools</h1>
    <p class="text-lg">
        Online tools for syncing, fixing and converting subtitle files.
    </p>


    <h3 class="mb-2">Syncing</h3>

    <div class="flex flex-wrap">

        @include('helpers.homepage-card', [
            'route' => route('shift'),
            'icon'  => 'clock',
            'title' => 'Subtitle Shifter',
            'text'  => 'Shift all timestamps of a subtitle file by an amount of milliseconds.',
        ])

        @include('helpers.homepage-card', [
            'route' => route('shiftPartial'),
            'icon'  => 'clock',
            'title' => 'Partial Subtitle Shifter',
            'text'  => 'Resync multiple specific parts of a subtitle file.',
        ])

    </div>


    <h3 class="mb-2">Converting</h3>

    <div class="flex flex-wrap">

        @include('helpers.homepage-card', [
            'route' => route('convertToSrt'),
            'icon'  => 'file',
            'title' => 'Convert to Srt',
            'text'  => 'Converts many types of text-based subtitle files to srt.',
        ])

        @include('helpers.homepage-card', [
            'route' => route('convertToVtt'),
            'icon'  => 'file',
            'title' => 'Convert to Vtt',
            'text'  => 'Converts many types of text-based subtitle files to Vtt.',
        ])

        @include('helpers.homepage-card', [
            'route' => route('subIdx'),
            'icon'  => 'file',
            'title' => 'Sub/Idx to Srt Converter',
            'text'  => 'Converts picture-based sub/idx subtitles to srt.',
        ])

        @include('helpers.homepage-card', [
            'route' => route('sup'),
            'icon'  => 'file',
            'title' => 'Sup to Srt Converter',
            'text'  => 'Coverts picture-based sup subtitles to srt.',
        ])

        @include('helpers.homepage-card', [
            'title' => 'Convert to Plain Text',
            'text'  => 'Convert text-based subtitles to plain text.',
            'route' => route('convertToPlainText'),
            'icon'  => 'file',
        ])

    </div>


    <h3 class="mb-2">Fixing and cleaning</h3>

    <div class="flex flex-wrap">

        @include('helpers.homepage-card', [
            'route' => route('cleanSrt'),
            'icon'  => 'wrench',
            'title' => 'Srt Cleaner',
            'text'  => 'Remove incorrect formatting and SDH from srt files.',
        ])

        @include('helpers.homepage-card', [
            'route' => route('convertToUtf8'),
            'icon'  => 'wrench',
            'title' => 'Convert to UTF-8',
            'text'  => 'Change text encoding of any file to UTF-8.',
        ])

    </div>


    <h3 class="mb-2">Other tools</h3>

    <div class="flex flex-wrap">

        @include('helpers.homepage-card', [
            'route' => route('pinyin'),
            'icon'  => 'language',
            'title' => 'Make Pinyin Subtitles',
            'text'  => 'Turn normal Chinese subtitles into romanized, pinyin subtitles.',
        ])

    </div>

@endsection
