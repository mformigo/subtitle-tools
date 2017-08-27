<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\LoadsGenericCues;
use App\Subtitles\TimingStrings;

class SrtCue extends GenericSubtitleCue implements TimingStrings, LoadsGenericCues
{
    public function __construct($source = null)
    {
        if($source === null) {
            return;
        }
        else if($source instanceof GenericSubtitleCue) {
            $this->loadGenericCue($source);
        }
        else {
            throw new \InvalidArgumentException("Invalid SrtCue source");
        }
    }

    public function setTimingFromString($string)
    {
        if(!self::isTimingString($string)) {
            throw new \Exception("Not a valid timing string ({$string})");
        }

        list($startTimecode, $endTimecode) = explode(" --> ", trim($string));

        $this->setTiming(
            $this->timecodeToMs($startTimecode),
            $this->timecodeToMs($endTimecode)
        );

        return $this;
    }

    public function getTimingString()
    {
        return $this->msToTimecode($this->startMs) . " --> " . $this->msToTimecode($this->endMs);
    }

    private function msToTimecode($ms)
    {
        if($ms < 0) {
            return "00:00:00,000";
        }

        if($ms >= 360000000) {
            return "99:59:59,999";
        }

        $SS = floor($ms / 1000);
        $MM = floor($SS / 60);
        $HH = floor($MM / 60);
        $MIL = $ms % 1000;
        $SS = $SS % 60;
        $MM = $MM % 60;

        $HH  = str_pad($HH,  2, "0", STR_PAD_LEFT);
        $MM  = str_pad($MM,  2, "0", STR_PAD_LEFT);
        $SS  = str_pad($SS,  2, "0", STR_PAD_LEFT);
        $MIL = str_pad($MIL, 3, "0", STR_PAD_LEFT);

        return "{$HH}:{$MM}:{$SS},{$MIL}";
    }

    private function timecodeToMs($timecode)
    {
        list($HH, $MM, $SS, $MIL) = preg_split("/(:|,)/", $timecode);

        return ($HH * 60 * 60 * 1000) +
               ($MM      * 60 * 1000) +
               ($SS           * 1000) +
               ($MIL                );
    }

    public function loadGenericCue(GenericSubtitleCue $genericCue)
    {
        $this->setTiming($genericCue->getStartMs(), $genericCue->getEndMs());

        $this->setLines($genericCue->getLines());

        return $this;
    }

    public function toArray()
    {
        $lines = [$this->getTimingString()];

        foreach($this->lines as $line) {
            $lines[] = $line;
        }

        $lines[] = "";

        return $lines;
    }

    public static function isTimingString($string)
    {
        $string = trim($string);

        if(!preg_match("/^\d{2}:[0-5]\d:[0-5]\d,\d{3} --> \d{2}:[0-5]\d:[0-5]\d,\d{3}$/", $string)) {
            return false;
        }

        list($startInt, $endInt) = explode(" --> ", str_replace([':', ','], '', $string));

        if($startInt > $endInt) {
            return false;
        }

        return true;
    }
}
