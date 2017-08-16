<?php

namespace App\Subtitles;

interface ShiftsCues
{
    public function shift($ms);

    public function shiftPartial($fromMs, $toMs, $ms);
}
