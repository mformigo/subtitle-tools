<?php

namespace App\Subtitles\PlainText;

use App\Facades\TextFileReader;
use App\Subtitles\PartialShiftsCues;
use App\Subtitles\ShiftsCues;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MicroDVD extends TextFile implements TransformsToGenericSubtitle, ShiftsCues, PartialShiftsCues
{
    use WithFileLines;

    protected $extension = "sub";

    public function __construct($source = null)
    {
        if($source !== null) {
            $this->loadFile($source);
        }
    }

    /**
     * Returns true if the $filePath file is a valid format for this class
     * @param $file
     * @return bool
     */
    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = TextFileReader::getLines($filePath);

        $validCues = 0;

        foreach($lines as $line) {
            if(MicroDVDCue::isTimingString($line)) {
                $validCues++;

                if($validCues === 3) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $generic = new GenericSubtitle();

        $generic->setFilePath($this->filePath);

        $generic->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        foreach($this->lines as $line) {
            if(MicroDVDCue::isTimingString($line)) {
                $generic->addCue(new MicroDVDCue($line));
            }
        }

        return $generic;
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
            if(MicroDVDCue::isTimingString($this->lines[$i])) {
                $microDvdCue = new MicroDVDCue($this->lines[$i]);

                if($microDvdCue->getStartMs() >= $fromMs && $microDvdCue->getEndMs() <= $toMs) {
                    $microDvdCue->shift($ms);

                    $this->lines[$i] = $microDvdCue->toString();
                }
            }
        }

        return $this;
    }
}
