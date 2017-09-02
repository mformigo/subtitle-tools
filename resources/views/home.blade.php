@extends('layout.base-template')

@section('title',       __('seo.title.home'))
@section('description', __('seo.description.home'))
@section('keywords',    __('seo.keywords.home'))

@section('content')

    @component('components.page-intro')

        @slot('title') Subtitle Tools @endslot

        Online tools for syncing, fixing and converting subtitle files
        <br/>
        <br/>
        Choose the tool you need from the main menu

    @endcomponent


    @component('components.text-section')

        @component('components.text-section-content', ['h2' => true])
            @slot('title') About @endslot

            The goal of this website is to offer most tools that a common user would need, for free and in a user-friendly way.
            These tools will work on Windows, Mac and Linux and don't require you to install freeware on your computer.
            This website is still being actively developed, and your feedback is greatly appreciated.
            If you have any ideas for improvements, new tools, better design, or if you found a mistake in one of the current tools,
            please don't hesitate to <a href="{{ route('contact') }}">send us a message</a>
        @endcomponent

        @component('components.text-section-content')
            @slot('title') Resyncing Subtitles @endslot

            There are two tools available for syncing subtitles.
            The first tool is the <a href="{{ route('shift') }}">subtitle shifter</a>.
            It shifts all the timestamps of a movie subtitle file.
            The shifter tool is used to synchronize subtitles with video when there is a small offset, that stays the same for the whole video.
            <br/><br/>
            The second tool, the <a href="{{ route('shiftPartial') }}">partial subtitle shifter</a>, is almost the same as the first resync tool.
            The difference is that with the partial shifter, you can shift multiple parts of the subtitles by entering a start timestamp and an end timestamp for each shift.
        @endcomponent

        @component('components.text-section-content')
            @slot('title') Converting Subtitles to Srt @endslot

            The <a href="{{ route('convertToSrt') }}">srt converter tool</a> is a simple conversion tool for changing different subtitle formats to srt.
            It is mostly used for converting newer formats (like ass or ssa) to srt, because some android and smartphone devices don't support the new format.
            If you want to convert sub/idx subtitles to srt, you have to use the <a href="{{ route('subIdx') }}">sub/idx converter</a>.
        @endcomponent

        @component('components.text-section-content')
            @slot('title') Cleaning Srt Files @endslot

            Srt files can display incorrectly when they aren't formatted properly.
            The <a href="{{ route('cleanSrt') }}">srt cleaner tool</a> cleans and fixes most problems subrip files can have.
        @endcomponent

        @component('components.text-section-content')
            @slot('title') Converting text files to UTF-8 @endslot

            Text encoding is a tricky subject. If subtitles (or other text files) are not correctly encoded, they will display gibberish.
            The <a href="{{ route('convertToUtf8') }}">UTF-8 converter tool</a> automatically detects the uploaded files encoding, and re-encodes it in a way that works on nearly all devices.
        @endcomponent

        @component('components.text-section-content')
            @slot('title') Chinese Subtitles to Pinyin Subtitles @endslot

            The <a href="{{ route('pinyin') }}">Pinyin Subtitles tool</a> is made especially for people studying Chinese.
            It turns normal Chinese subtitles in to pinyin subtitles.
            Pinyin subtitles help with understanding spoken Chinese, and are a great help when watching Chinese movies.
        @endcomponent

    @endcomponent

@endsection
