@extends('guest.layout.base-template')

@section('title',       __('seo.title.blogVlcSubtitleBoxes'))
@section('description', __('seo.description.blogVlcSubtitleBoxes'))
@section('keywords',    __('seo.keywords.blogVlcSubtitleBoxes'))

@include('helpers.disconnect-echo')

@section('content')

    <h1>How to fix VLC subtitles not displaying properly</h1>
    <p>
        This guide explains how to fix subtitles that show up as weird symbols, boxes, blocks, or question marks in VLC media player.
        Subtitles not working is a common problem with Chinese, Japanese, Korean and Arabic srt subtitles.
    </p>

    <img class="my-4" src="/images/blog/170901-broken-subtitles.png" alt="Unreadable subtitles in VLC" />

    <p>
        There are three possible reasons why subtitles don't work in VLC:
    </p>
    <ul class="mt-2 leading-normal">
        <li>The subtitles are not using UTF-8 text encoding</li>
        <li>You are using the wrong font in VLC</li>
        <li>The subtitles are broken</li>
    </ul>

    <h2>Encoding subtitles as UTF-8</h2>
    <p>
        If the subtitles are not encoded in unicode UTF-8, VLC does not know how to display them.
        The easiest way to find out if your subtitles are encoded properly is by opening them in Notepad.
    </p>
    <img class="mt-4" src="/images/blog/170901-vlc-subtitle-boxes-wrong-encoding.png" alt="Chinese subtitles opened in Notepad with wrong text encoding" />
    <img class="mb-4" src="/images/blog/170901-vlc-subtitle-boxes-right-encoding.png" alt="Chinese subtitles opened in Notepad with utf-8 text encoding" />
    <p>
        The first picture shows a file using Chinese text encoding, and can't be read by Notepad or VLC.
        The second picture is the same file converted to UTF-8, displaying correctly.
        You need to convert the subtitles to UTF-8 for them to be readable in VLC.
        <a class="font-bold" href="{{ route('convertToUtf8') }}">You can easily convert a text file to unicode with the convert to UTF-8 tool.</a>
    </p>

    <h2>Using a unicode font in VLC</h2>
    <p>
        If you are sure the file is encoded in UTF-8 but the subtitles still show up as boxes or weird symbols, you should make sure you are using the right font in VLC.
        <br/><br/>
        <strong>Step 1:</strong> open the VLC settings by pressing <kbd>Ctrl</kbd>+<kbd>P</kbd> or by going to Tools > Preferences
    </p>
    <img class="my-4" src="/images/blog/170901-change-font-in-vlc-step-1.png" alt="Changing font in VLC step 1: open VLC settings" />


    <p>
        <strong>Step 2:</strong> in the bottom left corner of the preferences screen underneath <i>show settings</i>, select the <b>all</b> radiobutton
    </p>
    <img class="my-4" src="/images/blog/170901-change-font-in-vlc-step-2.png" alt="Changing font in VLC step 2: show all settings" />


    <p>
        <strong>Step 3:</strong> expand the Subtitles / OSD item, and select <b>text renderer</b>.
        Now make sure the font is set to <b>Arial Unicode MS</b>, other unicode fonts should also work. If Arial Unicode MS is not in the list, you can <a class="font-bold" href="https://www.wfonts.com/font/arial-unicode-ms" target="_blank" rel="nofollow">download and install it from here</a>
    </p>
    <img class="my-4" src="/images/blog/170901-change-font-in-vlc-step-4.png" alt="Changing font in VLC step 4: use the Arial Unicode MS font" />
    <p>
        You might need to restart VLC after changing these font settings.
        VLC is now using a unicode font that can display all Chinese, Korean and Japanese characters correctly.
    </p>

    <h2>Still not working?</h2>
    <p>
        If you are sure your subtitles are <a href="{{ route('convertToUtf8') }}">encoded in UTF-8</a>, and you are using a unicode font in VLC, but your subtitles still don't work, then the subtitles are most probably broken.
        You can permanently break subtitles by trying to change the text encoding in a wrong way (like trying to save it with Notepad).
        If your subtitles still show up as question marks, there is a chance that your file is actually filled with question marks.
        Your best bet at this point is either to download the subtitles again, or look for other subtitles.
    </p>

@endsection
