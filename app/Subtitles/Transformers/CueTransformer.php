<?php

namespace App\Subtitles\Transformers;

use App\Subtitles\ContainsGenericCues;

interface CueTransformer
{
    /**
     * @param ContainsGenericCues $subtitle
     *
     * @return boolean False if no valid transformations have happened, true otherwise
     */
    public function transformCues(ContainsGenericCues $subtitle);
}
