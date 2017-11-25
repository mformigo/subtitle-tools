<?php

namespace App\Subtitles;

use App\Subtitles\PlainText\GenericSubtitleCue;

interface ContainsGenericCues
{
    public function addCue($cue);

    public function hasCues();

    /**
     * @param bool $sortCues
     * @return GenericSubtitleCue[]
     */
    public function getCues($sortCues = true);

    /**
     * Sort cues based on start time
     * @return $this
     */
    public function sortCues();

    /**
     * Remove all cues that do not have text lines
     * @return $this
     */
    public function removeEmptyCues();

    /**
     * Removes cues with identical start ms, end ms and lines
     * @return $this
     */
    public function removeDuplicateCues();

    /**
     * Removes all parentheses from the text lines, then removes empty cues
     * @return $this
     */
    public function stripParenthesesFromCues();

    /**
     * Removes all angle brackets from the text lines, then removes empty cues
     * @return $this
     */
    public function stripAngleBracketsFromCues();

    /**
     * Removes all curly brackets and lines containing .ass drawings from the text lines, then removes empty cues
     * @return $this
     */
    public function stripCurlyBracketsFromCues();
}
