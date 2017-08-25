<?php

namespace App\Subtitles;

use App\Subtitles\PlainText\GenericSubtitle;

interface LoadsGenericSubtitles
{
    public function loadGenericSubtitle(GenericSubtitle $genericSubtitle);
}
