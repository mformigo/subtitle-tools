<?php

namespace App\Subtitles;

interface PartialShiftsCues
{
    public function shiftPartial($fromMs, $toMs, $ms);
}
