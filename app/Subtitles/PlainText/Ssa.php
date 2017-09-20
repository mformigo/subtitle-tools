<?php

namespace App\Subtitles\PlainText;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Ssa extends Ass
{
    protected $extension = "ssa";

    protected $cueClass = SsaCue::class;

    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = app(\SjorsO\TextFile\Contracts\TextFileReaderInterface::class)->getLines($filePath);

        foreach($lines as $line) {
            if(SsaCue::isTimingString($line)) {
                return true;
            }
        }

        $maybeSsaFile = false;
        $sample = array_map('strtolower', array_slice($lines, 0, 10));

        foreach($sample as $string) {
            if(trim($string) === '[script info]') {
                $maybeSsaFile = true;
                break;
            }
        }

        if($maybeSsaFile) {
            if(preg_grep("/^\[v4 styles\]/i" , $lines)) {
                return true;
            }
        }

        return false;
    }
}
