<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\TextFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Srt extends TextFile
{
    protected $extension = ".srt";

    protected $cues = [];

    public function __construct()
    {
    }

    public function addCue(GenericSubtitleCue $cue)
    {

    }

    public function loadContent($string)
    {

    }

    public static function createFromFile($file)
    {

    }

    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        return false;
    }

    public function getContent()
    {
        // TODO: Implement getContent() method.
    }
}
