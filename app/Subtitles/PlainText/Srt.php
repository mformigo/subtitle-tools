<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\LoadsGenericCues;
use App\Subtitles\LoadsGenericSubtitles;
use App\Subtitles\TextFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Srt extends TextFile implements LoadsGenericSubtitles
{
    protected $extension = ".srt";

    protected $cues = [];

    public function __construct()
    {
    }

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

        return false;
    }

    public function getContent()
    {
        // TODO: Implement getContent() method.
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
