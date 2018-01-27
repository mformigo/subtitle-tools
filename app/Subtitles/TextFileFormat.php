<?php

namespace App\Subtitles;

use RuntimeException;

class TextFileFormat
{
    protected $formats = [
        \App\Subtitles\PlainText\WebVtt::class,
        \App\Subtitles\PlainText\Srt::class,
        \App\Subtitles\PlainText\Ssa::class,
        \App\Subtitles\PlainText\Ass::class,
        \App\Subtitles\PlainText\Smi::class,
        \App\Subtitles\PlainText\MicroDVD::class,
        \App\Subtitles\PlainText\Mpl2::class,
        \App\Subtitles\PlainText\PlainText::class,
    ];

    public function getMatchingFormat($file, $loadFile = true)
    {
        foreach ($this->formats as $class) {
            if ($class::isThisFormat($file)) {
                return $loadFile
                    ? (new $class)->loadFileFromFormat($file, $class)
                    : (new $class);
            }
        }

        throw new RuntimeException('The file is not a text file format');
    }
}
