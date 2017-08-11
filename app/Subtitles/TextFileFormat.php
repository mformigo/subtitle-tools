<?php

namespace App\Subtitles;

class TextFileFormat
{
    protected $formats = [
        \App\Subtitles\PlainText\Ass::class,
        \App\Subtitles\PlainText\Srt::class,
        \App\Subtitles\PlainText\PlainText::class,
    ];

    public function getMatchingFormat($file)
    {

    }
}
