@extends('layout.base-template')

@section('title',       __('seo.title.shift'))
@section('description', __('seo.description.shift'))
@section('keywords',    __('seo.keywords.shift'))

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Resync Subtitles</h1>
    <p>
        Online tool for permanently syncing subtitle files
        <br>
        <br>
        If you want to resync multiple parts of a subtitle file separately, use the <a href="{{ route('shiftPartial') }}">partial shifter tool</a>
    </p>

    @component('components.tool-form')

        @slot('title') Select a file to shift @endslot

        @slot('formats') Supported subtitle formats: srt, ass, ssa, smi, webvtt @endslot

        @slot('extraAfter')
            <label class="block">
                Shift (in milliseconds):
                <input class="field" placeholder="1000" type="number" name="milliseconds" value="{{ old('milliseconds') }}" required>
            </label>
        @endslot

        @slot('buttonText') Shift @endslot

    @endcomponent


    <h2>How this tool adjusts timings</h2>
    <p>
        This online tool will shift all the timings inside the subtitle files by the entered amount of milliseconds (a second is the same as 1000 milliseconds).
        After shifting the file, all the movie dialogue will appear earlier (if you entered a negative amount) or later (if you entered a positive amount).
        The resulting file is permanently synced up with the video.
        <br/><br/>
        The following subtitle formats can be resynced: srt, ssa, ass, smi and vtt.
        Multiple files can be uploaded at the same time, you can also upload a zip file.
    </p>

    <h3>When the sync won't work</h3>
    <p>
        This tool will only work correctly if the subtitles and the video are out of sync by the same amount the whole video long.
        If, for example, the first half of your subtitles have a delay of 5 seconds, and the second half by 10 seconds, then this tool won't work.
        In this case, you should use the <a href="{{ route('shiftPartial') }}">partial shifter tool</a>.
    </p>

    <h3>Sync subtitles in VLC media player</h3>
    <p>
        Temporarily fixing the subtitle sync in <a href="https://www.videolan.org/vlc/index.html" target="_blank" rel="nofollow">VLC media player</a> is easy.
        You can use the <kbd>G</kbd> shortcut to add a 50 millisecond delay, or the <kbd>H</kbd> key to sync it 50 milliseconds forwards.
        Once you have found the right amount of delay, you can use it in this tool to permanently fix the subtitles.
        <br/><br/>
        Another easy trick you can use to adjust the subtitle timings in VLC is <a href="https://superuser.com/questions/95760/how-can-i-re-sync-the-subtitle-and-the-video-using-vlc-media-player/811278#811278" target="_blank" rel="nofollow">described here</a>.
        This trick works as follows:
    </p>
    <ol>
        <li>Press <kbd>Shift</kbd>+<kbd>H</kbd> when you hear a specific sentence</li>
        <li>Press <kbd>Shift</kbd>+<kbd>J</kbd> when the sentence appears in the subtitles</li>
        <li>Press <kbd>Shift</kbd>+<kbd>K</kbd> to resync the subtitles</li>
    </ol>

@endsection
