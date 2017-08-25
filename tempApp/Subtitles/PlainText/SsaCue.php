<?php

namespace App\Subtitles\PlainText;

class SsaCue extends AssCue
{
    /**
     * @var string Unimportant information before timing
     */
    protected $cueFirstPart = 'Dialogue: Marked=0,';

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

        if(!preg_match("/^Dialogue: Marked=\d,\d:[0-5]\d:[0-5]\d\.\d{2},\d:[0-5]\d:[0-5]\d\.\d{2},/i", $string)) {
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
