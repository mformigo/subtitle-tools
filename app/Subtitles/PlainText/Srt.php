<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\ContainsGenericCues;
use App\Subtitles\LoadsGenericSubtitles;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Srt extends TextFile implements LoadsGenericSubtitles
{
    use WithFileLines, ContainsGenericCues;

    protected $extension = "srt";

    /**
     * @var SrtCue[]
     */
    protected $cues = [];

    public function __construct($source = null)
    {
        if($source === null) {
            return;
        }
        else if($source instanceof TransformsToGenericSubtitle) {
            $this->loadGenericSubtitle($source->toGenericSubtitle());
        }
        else {
            throw new \InvalidArgumentException("Invalid Srt source");
        }
    }

    public function addCue($cue)
    {
        if($cue instanceof SrtCue) {
            $this->cues[] = $cue;
        }
        else if($cue instanceof GenericSubtitleCue) {
            $this->cues[] = new SrtCue($cue);
        }
        else {
            throw new \InvalidArgumentException("Invalid cue");
        }

        return $this;
    }

    public function getContent()
    {
        usort($this->cues, function(SrtCue $a, SrtCue $b) {
            return $a->getStartMs() <=> $b->getStartMs();
        });

        $id = 1;
        $lines = [];

        foreach($this->cues as $cue) {
            $lines[] = $id++;

            $lines = array_merge($lines, $cue->toArray());
        }

        return implode("\r\n", $lines);
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
