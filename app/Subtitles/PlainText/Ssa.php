<?php

namespace App\Subtitles\PlainText;

class Ssa extends Ass
{
    protected $extension = "ssa";

    protected $cueClass = SsaCue::class;

    public static function isThisFormat($file)
    {
        $lines = read_lines($file);

        foreach ($lines as $line) {
            if (SsaCue::isTimingString($line)) {
                return true;
            }
        }

        $maybeSsaFile = false;

        $sample = array_map('strtolower', array_slice($lines, 0, 10));

        foreach ($sample as $string) {
            if (trim($string) === '[script info]') {
                $maybeSsaFile = true;
                break;
            }
        }

        if ($maybeSsaFile) {
            if (preg_grep("/^\[v4 styles\]/i" , $lines)) {
                return true;
            }
        }

        return false;
    }
}
