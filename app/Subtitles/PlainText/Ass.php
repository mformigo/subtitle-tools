<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Ass extends TextFile implements TransformsToGenericSubtitle
{
    use WithFileLines;

    protected $extension = "ass";

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $generic = new GenericSubtitle();

        $generic->setFilePath($this->filePath);

        $generic->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        foreach($this->lines as $line) {
            if(AssCue::isTimingString($line)) {
                $assCue = new AssCue();

                $assCue->loadString($line);

                $generic->addCue($assCue);
            }
        }

        return $generic;
    }

    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = app('TextFileReader')->getLines($filePath);

        foreach($lines as $line) {
            if(AssCue::isTimingString($line)) {
                return true;
            }
        }

        $maybeAssFile = false;
        $sample = array_map('strtolower', array_slice($lines, 0, 10));

        foreach($sample as $string) {
            if(trim($string) === '[script info]') {
                $maybeAssFile = true;
                break;
            }
        }

        if($maybeAssFile) {
            if(preg_grep("/^\[v4\+ styles\]/i" , $lines)) {
                return true;
            }
        }

        return false;
    }
}
