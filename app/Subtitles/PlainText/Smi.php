<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Smi extends TextFile implements TransformsToGenericSubtitle
{
    use WithFileLines;

    protected $extension = ".smi";

    protected $cues = [];

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $genericSubtitle = new GenericSubtitle();

        $genericSubtitle->setFilePath($this->filePath);

        $genericSubtitle->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        // Smi files ignore new lines, the easiest way to parse the file is by removing new lines.
        $cleanContent = implode('', $this->lines);

        if(stripos($cleanContent, "<sync ") === false) {
            return $genericSubtitle;
        }

        $fromIndex = stripos($cleanContent, "<sync ");

        while($fromIndex !== false && $fromIndex < strlen($cleanContent))
        {
            $toIndex = stripos($cleanContent, "<sync ", $fromIndex + 1);

            if($toIndex === false) {
                $toIndex = strlen($cleanContent);
            }

            $maybeCueText = substr($cleanContent, $fromIndex, $toIndex - $fromIndex);

            if(preg_match('/^<sync .*?start=(?:"?|\'?)(?<startMs>\d+).*?>/i', $maybeCueText, $startTag))
            {
                $startMs = $startTag['startMs'];
                $endMs   = $startTag['startMs'];

                if(preg_match('/^<sync .*?end=(?:"?|\'?)(?<endMs>\d+).*?>/i', $maybeCueText, $endTag)) {
                    $endMs = $endTag['endMs'];
                }

                // Smi cues end when the next cue starts, except when they have an end= attribute
                if($genericSubtitle->hasCues()) {
                    $previousCue = $genericSubtitle->getCues()[count($genericSubtitle->getCues()) - 1];

                    if($previousCue->getStartMs() === $previousCue->getEndMs()) {
                        $previousCue->setTiming($previousCue->getStartMs(), $startMs);
                    }
                }

                if($startMs >= $endMs) {
                    $genericCue = new GenericSubtitleCue();

                    $genericCue->setTiming($startMs, $endMs);

                    $genericCue->setLines(preg_split("~<br>|<br/>|<br />~i", $maybeCueText));

                    $genericSubtitle->addCue($genericCue);
                }
            }

            $fromIndex = $toIndex;
        }

        // The last cue in the file needs to have its end time set manually (if it did not have an end= attribute)
        if($genericSubtitle->hasCues()) {
            $lastCue = $genericSubtitle->getCues()[count($genericSubtitle->getCues()) - 1];

            if($lastCue->getStartMs() === $lastCue->getEndMs()) {
                $lastCue->setTiming($lastCue->GetStartMs(), $lastCue->GetStartMs() + 3000);
            }
        }

        $genericSubtitle->stripAngleBracketsFromCues();

        return $genericSubtitle;
    }

    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = app('TextFileReader')->getLines($filePath);

        return false;
    }
}
