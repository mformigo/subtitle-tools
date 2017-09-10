@extends('guest.layout.base-template')

@section('title',       __('seo.title.blogVlcSubtitleBoxes'))
@section('description', __('seo.description.blogVlcSubtitleBoxes'))
@section('keywords',    __('seo.keywords.blogVlcSubtitleBoxes'))

@section('content')

    <section class="blog-intro">
        <div class="container">
            <h1>How to fix VLC subtitles not displaying properly</h1>
            <p>
                This guide explains how to fix subtitles that show up as weird symbols, boxes, blocks, or question marks in VLC media player.
                Subtitles not working is a common problem with Chinese, Japanese, Korean and Arabic srt subtitles.
            </p>
            <img src="/images/blog/170901-broken-subtitles.png" alt="Unreadable subtitles in VLC" />
        </div>
    </section>

    <section class="blog-text-content">
        <div class="container">

            <p>
                There are three possible reasons why subtitles don't work in VLC:
            </p>
            <ul>
                <li>The subtitles are not using UTF-8 text encoding</li>
                <li>You are using the wrong font in VLC</li>
                <li>The subtitles are broken</li>
            </ul>

            <h2>Encoding subtitles as UTF-8</h2>
            <p>
                If the subtitles are not encoded in unicode UTF-8, VLC does not know how to display them.
                The easiest way to find out if your subtitles are encoded properly is by opening them in Notepad.
            </p>
            <img src="/images/blog/170901-vlc-subtitle-boxes-wrong-encoding.png" alt="Chinese subtitles opened in Notepad with wrong text encoding" />
            <img src="/images/blog/170901-vlc-subtitle-boxes-right-encoding.png" alt="Chinese subtitles opened in Notepad with utf-8 text encoding" />
            <p>
                The first picture shows a file using Chinese text encoding, and can't be read by Notepad or VLC.
                The second picture is the same file converted to UTF-8, displaying correctly.
                You need to convert the subtitles to UTF-8 for them to be readable in VLC.
                <strong>You can easily convert a text file to unicode with the  <a href="{{ route('convertToUtf8') }}">convert to UTF-8 tool</a>.</strong>
            </p>

            <h2>Using a unicode font in VLC</h2>
            <p>
                If you are sure the file is encoded in UTF-8 but the subtitles still show up as boxes or weird symbols, you should make sure you are using the right font in VLC.
                <br/><br/>
                Step 1: open the VLC settings by pressing <kbd>Ctrl</kbd>+<kbd>P</kbd> or by going to Tools > Preferences
            </p>
            <img src="/images/blog/170901-change-font-in-vlc-step-1.png" alt="Changing font in VLC step 1: open VLC settings" />
            <p>
                Step 2: in the bottom left corner of the preferences screen underneath <i>show settings</i>, select the <b>all</b> radiobutton
            </p>
            <img src="/images/blog/170901-change-font-in-vlc-step-2.png" alt="Changing font in VLC step 2: show all settings" />
            <p>
                Step 3: You are now in the advanced preferences screen. At the bottom of the list, select <b>Subtitles / OSD</b>, and make sure the text rendering module is set to <b>Freetype 2 font renderer</b>.
            </p>
            <img src="/images/blog/170901-change-font-in-vlc-step-3.png" alt="Changing font in VLC step 3: use freetype 2 font renderer" />
            <p>
                Step 4: expand the Subtitles / OSD item, and select <b>text renderer</b>.
                Now make sure the font is set to <b>Arial Unicode MS</b>, other unicode fonts should also work.
            </p>
            <img src="/images/blog/170901-change-font-in-vlc-step-4.png" alt="Changing font in VLC step 4: use the Arial Unicode MS font" />
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

        </div>
    </section>

@endsection
