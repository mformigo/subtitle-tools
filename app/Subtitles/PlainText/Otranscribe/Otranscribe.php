<?php

namespace App\Subtitles\PlainText\Otranscribe;

use App\Subtitles\PlainText\GenericSubtitle;
use App\Subtitles\PlainText\GenericSubtitleCue;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;

class Otranscribe extends TextFile implements TransformsToGenericSubtitle
{
    use WithFileLines;

    public function toGenericSubtitle()
    {
        $genericSubtitle = new GenericSubtitle();

        $genericSubtitle->setFilePath($this->filePath);

        $genericSubtitle->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        $cues = [];

        $cueLines = array_filter($this->lines, function ($line) {
            return OtranscribeCue::isTimingString($line);
        });

        /** @var GenericSubtitleCue $previousCue */
        $previousCue = null;

        foreach ($cueLines as $line) {
            $oTranscribeCue = new OtranscribeCue($line);

            if ($previousCue !== null) {
                $previousCue->setTiming($previousCue->getStartMs(), $oTranscribeCue->getEndMs());
            }

            $cues[] = $previousCue = $oTranscribeCue->toGenericCue();
        }

        if ($previousCue !== null) {
            $previousCue->setTiming($previousCue->getStartMs(), $previousCue->getEndMs() + 4000);
        }

        return $genericSubtitle->addCues($cues)->removeEmptyCues();
    }

    public static function isThisFormat($file)
    {
        $lines = read_lines($file);

        $seenLines = 0;
        $validCues = 0;

        foreach ($lines as $line) {
            $seenLines++;

            if (OtranscribeCue::isTimingString($line)) {
                $validCues++;

                if ($validCues === 3) {
                    return true;
                }
            }

            if ($seenLines > 10) {
                return false;
            }
        }

        return false;
    }
}
