<?php

namespace App\Subtitles\PlainText;

class SrtTimecode
{
    protected $milliseconds;

    protected $timecode;

    public function __construct($timecode)
    {
        if (preg_match('/^\d+$/', $timecode)) {
            $this->milliseconds = $timecode;
        } else {
            $timecode = strtolower(
                trim($timecode)
            );

            if (! preg_match('/^\d\d?: ?[0-5]\d: ?[0-5]\d(,|\.)\d\d\d?$/', $timecode)) {
                return;
            }

            $this->milliseconds = $this->timecodeToMs($timecode);
        }

        $this->timecode = $this->msToTimecode($this->milliseconds);
    }

    protected function timecodeToMs($timecode)
    {
        $timecode = str_replace([' ', '-'], '',  $timecode);

        $timecode = str_replace('.', ',', $timecode);

        [$HH, $MM, $SS, $MIL] = preg_split('/(:|,)/', $timecode);

        // Fix for timings with only two digits for the milliseconds.
        // example: 00:00:01,26 should be 260ms, not 26ms.
        if (strlen($MIL) === 2) {
            $MIL = $MIL * 10;
        }

        return ($HH * 60 * 60 * 1000) + ($MM * 60 * 1000) + ($SS * 1000) + $MIL;
    }

    protected function msToTimecode($milliseconds)
    {
        if ($milliseconds < 0) {
            return '00:00:00,000';
        }

        if ($milliseconds >= 360000000) {
            return '99:59:59,999';
        }

        $SS = floor($milliseconds / 1000);
        $MM = floor($SS / 60);
        $HH = floor($MM / 60);
        $MIL = $milliseconds % 1000;
        $SS = $SS % 60;
        $MM = $MM % 60;

        $HH  = str_pad($HH,  2, '0', STR_PAD_LEFT);
        $MM  = str_pad($MM,  2, '0', STR_PAD_LEFT);
        $SS  = str_pad($SS,  2, '0', STR_PAD_LEFT);
        $MIL = str_pad($MIL, 3, '0', STR_PAD_LEFT);

        return "{$HH}:{$MM}:{$SS},{$MIL}";
    }

    public function valid()
    {
        return $this->milliseconds !== null;
    }

    public function invalid()
    {
        return ! $this->valid();
    }

    public function timecode()
    {
        return $this->timecode;
    }

    public function milliseconds()
    {
        return $this->milliseconds;
    }
}
