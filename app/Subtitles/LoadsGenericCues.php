<?php

namespace App\Subtitles;

use App\Subtitles\PlainText\GenericSubtitleCue;

interface LoadsGenericCues
{
    public function loadGenericCue(GenericSubtitleCue $genericCue);
}
