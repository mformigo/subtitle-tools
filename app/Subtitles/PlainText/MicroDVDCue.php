<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\TimingStrings;
use App\Subtitles\TransformsToGenericCue;

class MicroDVDCue extends GenericSubtitleCue implements TimingStrings, TransformsToGenericCue
{
    protected $frameRate = 23.976;

    public function __construct($timingLine = null)
    {
        if($timingLine !== null) {
            $this->loadString($timingLine);
        }
    }

    public function setFps($fps)
    {
        if(!is_float($fps) && !preg_match('/\d\d\.\d+/', $fps)) {
            throw new \Exception("Invalid framerate ({$fps})");
        }

        $this->frameRate = (float)$fps;
    }

    public function getFps()
    {
        return $this->frameRate;
    }

    public function loadString($string)
    {
        $this->setTimingFromString($string);

        $timingOffset = strlen($this->startMs) + strlen($this->endMs) + 4;

        $this->setLines(
            explode('|', substr($string, $timingOffset))
        );

        return $this;
    }

    public function setTimingFromString($string)
    {
        if(!static::isTimingString($string)) {
            throw new \Exception("Not a valid " . get_class($this) . " cue string ({$string})");
        }

        $string = trim($string);

        preg_match('/^\{(\d+)\}\{(\d+)\}.+/', $string, $matches);

        list($line, $startMs, $endMs) = collect($matches)->filter(function($str) {
            return strlen($str) !== 0;
        })->values()->all();

        $this->setTiming($startMs, $endMs);
    }

    public function getTimingString()
    {
        return $this->toString();
    }

    public function toString()
    {
        $textPart = implode('|', $this->lines);

        $startFrame = $this->startMs;
        $endFrame = $this->endMs;

        return "{{$startFrame}}{{$endFrame}}{$textPart}";
    }

    public function toGenericCue()
    {
        $genericCue = new GenericSubtitleCue();

        // This shouldn't be necessary for MicroDVD cues, but i see it happen a lot
        // Italic lines start with a slash
        $italicLines = array_map(function($line) {
            if(strpos($line, '/') === 0) {
                return '<i>' . substr($line, 1) . '</i>';
            }
            return $line;
        }, $this->lines);

        $genericCue->setLines($italicLines);

        $genericCue->setTiming($this->getStartMs(), $this->getEndMs());

        return $genericCue;
    }

    public function getStartMs()
    {
        $frameNumber = $this->startMs;

        return (int)round(($frameNumber / $this->frameRate) * 1000);
    }

    public function getEndMs()
    {
        $frameNumber = $this->endMs;

        return (int)round(($frameNumber / $this->frameRate) * 1000);
    }

    public static function isTimingString($string)
    {
        $string = trim($string);

        // Match ^{0}{0}.+
        if(!preg_match('/^\{(\d+)\}\{(\d+)\}.+/', $string, $matches)) {
            return false;
        }

        list($line, $startMs, $endMs) = collect($matches)->filter(function($str) {
            return strlen($str) !== 0;
        })->values()->all();

        if($startMs > $endMs) {
            return false;
        }

        return true;
    }
}
