<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\TimingStrings;
use App\Subtitles\TransformsToGenericCue;

class Mpl2Cue extends GenericSubtitleCue implements TimingStrings, TransformsToGenericCue
{
    public function __construct($timingLine = null)
    {
        if($timingLine !== null) {
            $this->loadString($timingLine);
        }
    }

    public function loadString($string)
    {
        $this->setTimingFromString($string);

        $timingOffset = strlen($this->startMs / 100) +
                        strlen($this->endMs   / 100) + 4;

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

        preg_match('/^\[(\d+)\]\[(\d+)\].+/', $string, $matches);

        list($line, $startDecaseconds, $endDecaseconds) = collect($matches)->filter(function($str) {
            return strlen($str) !== 0;
        })->values()->all();

        $this->setTiming(
            $startDecaseconds * 100,
            $endDecaseconds * 100
        );
    }

    public function getTimingString()
    {
        return $this->toString();
    }

    public function toString()
    {
        $textPart = implode('|', $this->lines);

        $startDecaseconds = $this->startMs / 100;
        $endDecaseconds = $this->endMs / 100;

        return "[{$startDecaseconds}][{$endDecaseconds}]{$textPart}";
    }

    public function toGenericCue()
    {
        $genericCue = new GenericSubtitleCue();

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

    public static function isTimingString($string)
    {
        $string = trim($string);

        // Match ^[0][0].+
        if(!preg_match('/^\[(\d+)\]\[(\d+)\].+/', $string, $matches)) {
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
