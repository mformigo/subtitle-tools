<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\TimingStrings;
use App\Subtitles\TransformsToGenericCue;

class MicroDVDCue extends GenericSubtitleCue implements TimingStrings, TransformsToGenericCue
{
    // MicroDVD cues can either use {0}{1} or [0][1]
    protected $usesCurlyBrackets = true;

    public function __construct($timingLine = null)
    {
        if($timingLine !== null) {
            $this->loadString($timingLine);
        }
    }

    public function loadString($string)
    {
        $this->setTimingFromString($string);

        $timingOffset = strlen($this->getStartMs()) + strlen($this->getEndMs()) + 4;

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

        $this->usesCurlyBrackets = $string[0] === '{';

        preg_match('/^(?:\{(\d+)\}\{(\d+)\}|\[(\d+)\]\[(\d+)\]).+/', $string, $matches);

        list($line, $startMs, $endMs) = collect($matches)->filter(function($str) {
            return !empty($str);
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

        if($this->usesCurlyBrackets) {
            return "{{$this->getStartMs()}}{{$this->getEndMs()}}{$textPart}";
        }
        else {
            return "[{$this->getStartMs()}][{$this->getEndMs()}]{$textPart}";
        }
    }

    public function toGenericCue()
    {
        $genericCue = new GenericSubtitleCue();

        $genericCue->setLines($this->lines);

        $genericCue->setTiming($this->getStartMs(), $this->getEndMs());

        return $genericCue;
    }

    public static function isTimingString($string)
    {
        $string = trim($string);

        // Match either ^{0}{0}.+ or ^[0][0].+
        if(!preg_match('/^(?:\{(\d+)\}\{(\d+)\}|\[(\d+)\]\[(\d+)\]).+/', $string, $matches)) {
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
