<?php

namespace App\Subtitles;

interface TimingStrings
{
    public static function isTimingString($string);

    public function setTimingFromString($string);

    public function getTimingString();
}
