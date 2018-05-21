<?php

namespace App\Subtitles\Transformers;

use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PlainText\GenericSubtitleCue;

abstract class CueTransformer
{
    /**
     * Transform the lines of a cue.
     *
     * @param ContainsGenericCues $subtitle
     *
     * @return bool Returns "false" if no lines were transformed, "true" otherwise.
     */
    public function transformCues(ContainsGenericCues $subtitle): bool
    {
        $hasTransformedSomething = false;

        foreach ($subtitle->getCues(false) as $cue) {
            $cueChanged = $this->transformCue($cue);

            if (! $hasTransformedSomething && $cueChanged) {
                $hasTransformedSomething = true;
            }
        }

        return $hasTransformedSomething;
    }

    public function transformCue(GenericSubtitleCue $cue): bool
    {
        $originalLines = $cue->getLines();

        $transformedLines = $this->transformLines($originalLines);

        $cue->setLines($transformedLines);

        return $originalLines !== $transformedLines;
    }

    abstract public function transformLines(array $lines): array;
}
