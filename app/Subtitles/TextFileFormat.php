<?php

namespace App\Subtitles;

class TextFileFormat
{
    protected $formats = [
        \App\Subtitles\PlainText\Ass::class,
        \App\Subtitles\PlainText\Srt::class,
        \App\Subtitles\PlainText\Smi::class,
        \App\Subtitles\PlainText\PlainText::class,
    ];

    public function getMatchingFormat($file)
    {
        foreach($this->formats as $class) {
            if($class::isThisFormat($file)) {
                return (new $class)->loadFile($file);
            }
        }

        throw new \Exception('The file is not a text file format');
    }
}
