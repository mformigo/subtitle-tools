<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\LoadsGenericSubtitles;
use App\Subtitles\TextFile;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Srt extends TextFile implements LoadsGenericSubtitles
{
    use WithFileLines;

    protected $extension = ".srt";

    protected $cues = [];

    public function addCue($cue)
    {
        if($cue instanceof SrtCue) {
            $this->cues[] = $cue;
        }
        else if($cue instanceof GenericSubtitleCue) {
            $this->cues[] = (new SrtCue())->loadGenericCue($cue);
        }
        else {
            throw new \Exception("Invalid cue");
        }

        return $this;
    }

    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = app('TextFileReader')->getLines($filePath);

        // todo: make matching more strict by also checking for an id on the previous line (?)

        foreach($lines as $line) {
            if(SrtCue::isTimingString($line)) {
                return true;
            }
        }

        return false;
    }

    public function loadGenericSubtitle(GenericSubtitle $genericSubtitle)
    {
        $this->setFilePath($genericSubtitle->getFilePath());

        $this->setFileNameWithoutExtension($genericSubtitle->getFileNameWithoutExtension());

        foreach($genericSubtitle->getCues() as $genericCue) {
            $this->addCue($genericCue);
        }

        return $this;
    }
}
