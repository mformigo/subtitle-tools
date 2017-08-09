<?php

namespace App\Subtitles\PlainText;

class SrtCue extends GenericSubtitleCue
{
    public static function isTimingString($string)
    {
        $string = trim($string);

        if(!preg_match("/^\d{2}:[0-5]\d:[0-5]\d,\d{3} --> \d{2}:[0-5]\d:[0-5]\d,\d{3}$/", $string)) {
            return false;
        }

        list($startTimeInt, $endTimeInt) = explode(" --> ", str_replace([':', ','], '',$string));

        if($startTimeInt > $endTimeInt) {
            return false;
        }

        return true;
    }

    public function setTimingFromString($string)
    {
        if(!self::isTimingString($string)) {
            throw new \Exception("Not a valid timing string ({$string})");
        }

        list($startTimecode, $endTimecode) = explode(" --> ", $string);

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
}
