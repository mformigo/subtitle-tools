<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\TextFile;
use App\Subtitles\TransformToGenericSubtitle;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Ass extends TextFile implements TransformToGenericSubtitle
{
    use WithFileLines;

    protected $extension = ".ass";

    public function __construct()
    {
    }

    public function toGenericSubtitle()
    {
        $generic = new GenericSubtitle();

        $generic->setFilePath($this->filePath)
            ->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        foreach($this->lines as $line) {

        }
    }

    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        return false;
    }
}
