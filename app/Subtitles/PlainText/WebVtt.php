<?php

namespace App\Subtitles\PlainText;

use SjorsO\TextFile\Facades\TextFileReader;
use App\Subtitles\PartialShiftsCues;
use App\Subtitles\ShiftsCues;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WebVtt extends TextFile implements ShiftsCues, PartialShiftsCues, TransformsToGenericSubtitle
{
    use WithFileLines;

    protected $extension = "vtt";

    public function __construct($source = null)
    {
        if ($source === null) {
            return;
        }
        else {
            $this->loadFile($source);
        }
    }

    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = TextFileReader::getLines($filePath);

        if (count($lines) === 0) {
            return false;
        }

        // First line starting with WEBVTT is always a WebVtt file
        if (strpos(trim($lines[0]), 'WEBVTT') === 0) {
            return true;
        }

        return false;
    }

    public function shift($ms)
    {
        return $this->shiftPartial(0, PHP_INT_MAX, $ms);
    }

    public function shiftPartial($fromMs, $toMs, $ms)
    {
        if ($fromMs > $toMs || $ms == 0) {
            return $this;
        }

        for ($i = 1; $i < count($this->lines); $i++) {
            if (WebVttCue::isTimingString($this->lines[$i])) {
                $cue = (new WebVttCue)->setTimingFromString($this->lines[$i]);

                if ($cue->getStartMs() >= $fromMs && $cue->getStartMs() <= $toMs) {
                    $cue->shift($ms);

                    $this->lines[$i] = $cue->getTimingString();
                }
            }
        }

        return $this;
    }

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $generic = new GenericSubtitle();

        $generic->setFilePath($this->filePath);

        $generic->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        for ($i = 1; $i < count($this->lines); $i++) {
            if (WebVttCue::isTimingString($this->lines[$i])) {
                $newGenericCue = new GenericSubtitleCue();

                $webVttTiming = (new WebVttCue)->setTimingFromString($this->lines[$i]);

                $newGenericCue->setTiming(
                    $webVttTiming->getStartMs(),
                    $webVttTiming->getEndMs()
                );

                while (++$i < count($this->lines) && !empty(trim($this->lines[$i]))) {
                    $newGenericCue->addLine($this->lines[$i]);
                }

                $generic->addCue($newGenericCue);
            }
        }

        return $generic;
    }
}
