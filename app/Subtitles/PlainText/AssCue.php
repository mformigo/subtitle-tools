<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\TimingStrings;
use App\Subtitles\TransformsToGenericCue;

class AssCue extends GenericSubtitleCue implements TimingStrings, TransformsToGenericCue
{
    /**
     * @var string Unimportant information before timing
     */
    protected $cueFirstPart = 'Dialogue: 0,';

    /**
     * @var string Unimportant part between timing string and text
     */
    protected $cueMiddlePart = ',*Default,NTP,0,0,0,,';

    public function __construct($timingLine = null)
    {
        if($timingLine !== null) {
            $this->loadString($timingLine);
        }
    }

    public function loadString($string)
    {
        $this->setTimingFromString($string);

        // Everything after the 9th comma is treated as the subtitle text, this can include commas
        $parts = explode(',', $string, 10);

        $this->cueFirstPart = "{$parts[0]},";

        $this->cueMiddlePart = ',' . implode(',', array_slice($parts, 3, 6)) . ',';

        $this->setLines(
            explode("\n", str_replace('\N', "\n", $parts[9]))
        );

        return $this;
    }

    public function setTimingFromString($string)
    {
        if(!static::isTimingString($string)) {
            throw new \Exception("Not a valid " . get_class($this) . " cue string ({$string})");
        }

        $parts = explode(',', trim($string));

        $this->setTiming(
          $this->timecodeToMs($parts[1]),
          $this->timecodeToMs($parts[2])
        );
    }

    public function getTimingString()
    {
        return $this->toString();
    }

    private function msToTimecode($ms)
    {
        if($ms < 0) {
            return '0:00:00.00';
        }

        if($ms >= 36000000) {
            return '9:59:59.99';
        }

        $SS = floor($ms / 1000);
        $MM = floor($SS / 60);
        $H = floor($MM / 60);
        $MIL = $ms % 1000;
        $SS = $SS % 60;
        $MM = $MM % 60;

        $MM = str_pad($MM, 2, "0", STR_PAD_LEFT);
        $SS = str_pad($SS, 2, "0", STR_PAD_LEFT);
        // Remove the last digit from the milliseconds, ass only has two digits there
        $MIL = substr(str_pad($MIL, 3, "0", STR_PAD_LEFT), 0, 2);

        return "{$H}:{$MM}:{$SS}.{$MIL}";
    }

    private function timecodeToMs($timecode)
    {
        list($H, $MM, $SS, $MS) = preg_split("/(:|\.)/", $timecode);

        // The milliseconds are only two digits long, so a zero needs to be added
        return ($H * 60 * 60 * 1000) +
               ($MM     * 60 * 1000) +
               ($SS          * 1000) +
               ($MS * 10           );
    }

    public function toString()
    {
        $timingPart = $this->msToTimecode($this->startMs) . "," . $this->msToTimecode($this->endMs);

        $textPart = implode('\N', $this->lines);

        return $this->cueFirstPart . $timingPart . $this->cueMiddlePart . $textPart;
    }

    public function toGenericCue()
    {
        $genericCue = new GenericSubtitleCue();

        $genericCue->setLines($this->lines);

        $genericCue->setTiming($this->startMs, $this->endMs);

        return $genericCue;
    }

    public static function isTimingString($string)
    {
        $string = trim($string);

        if(stripos($string, 'Dialogue: ') !== 0) {
            return false;
        }

        $parts =  explode(',', $string, 10);

        if(count($parts) !== 10) {
            return false;
        }

        if(!preg_match("/^Dialogue: \d+,\d:[0-5]\d:[0-5]\d\.\d{2},\d:[0-5]\d:[0-5]\d\.\d{2},/i", $string)) {
            return false;
        }

        $startInt = str_replace([':', '.'], '', $parts[1]);
        $endInt   = str_replace([':', '.'], '', $parts[2]);

        if($startInt > $endInt) {
            return false;
        }

        return true;
    }
}
