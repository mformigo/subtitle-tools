<?php

namespace App\Subtitles;

use App\Subtitles\PlainText\GenericSubtitle;

interface TransformsToGenericSubtitle
{
    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle();
}
