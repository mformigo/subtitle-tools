<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\PartialShiftsCues;
use App\Subtitles\ShiftsCues;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Ass extends TextFile implements TransformsToGenericSubtitle, ShiftsCues, PartialShiftsCues
{
    use WithFileLines;

    protected $extension = "ass";

    protected $cueClass = AssCue::class;

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $generic = new GenericSubtitle();

        $generic->setFilePath($this->filePath);

        $generic->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        foreach($this->lines as $line) {
            if($this->cueClass::isTimingString($line)) {
                $assCue = new $this->cueClass;

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

    public function shift($ms)
    {
        return $this->shiftPartial(0, PHP_INT_MAX, $ms);
    }

    public function shiftPartial($fromMs, $toMs, $ms)
    {
        if($fromMs > $toMs || $ms == 0) {
            return $this;
        }

        for($i = 0; $i < count($this->lines); $i++) {
            if($this->cueClass::isTimingString($this->lines[$i])) {
                $assCue = new $this->cueClass($this->lines[$i]);

                if($assCue->getStartMs() >= $fromMs && $assCue->getEndMs() <= $toMs) {
                    $assCue->shift($ms);

                    $this->lines[$i] = $assCue->toString();
                }
            }
        }

        return $this;
    }
}
