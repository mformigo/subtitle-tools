<?php

namespace App\Subtitles\Tools;

use App\Subtitles\PlainText\GenericSubtitleCue;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\Tools\Options\SrtCleanerOptions;
use App\Subtitles\Transformers\StripSpeakerLabels;

class SrtCleaner
{
    public function clean(Srt $srt, SrtCleanerOptions $options): void
    {
        if ($options->stripParentheses) {
            $srt->stripParenthesesFromCues();
        }

        if ($options->stripCurly) {
            $srt->stripCurlyBracketsFromCues();
        }

        if ($options->stripAngle) {
            $srt->stripAngleBracketsFromCues();
        }

        if ($options->stripSquare) {
            $srt->stripSquareBracketsFromCues();
        }

        if ($options->stripSpeakerLabels) {
            (new StripSpeakerLabels)->transformCues($srt);

            $srt->removeEmptyCues();
        }

        if ($options->stripCuesWithMusicNote) {
            $srt->filterCues(function (GenericSubtitleCue $cue) {
                return mb_strpos($cue->getLinesAsText(), '♪') === false;
            });
        }

        $srt->removeDuplicateCues();
    }
}
