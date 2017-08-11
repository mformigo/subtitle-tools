<?php

namespace App\Subtitles;

use App\Subtitles\PlainText\GenericSubtitleCue;

trait ContainsGenericCues
{
    /**
     * @var GenericSubtitleCue[]
     */
    protected $cues = [];

    public function addCue(GenericSubtitleCue $cue)
    {
        $this->cues[] = $cue;
    }

    public function hasCues()
    {
        return count($this->cues) > 0;
    }

    /**
     * Remove all cues that do not have text lines
     * @return $this
     */
    public function removeEmptyCues()
    {
        $this->cues = array_filter($this->cues, function(GenericSubtitleCue $cue) {
            return $cue->hasLines();
        });

        // Reset the array keys
        $this->cues = array_values($this->cues);

        return $this;
    }

    /**
     * Removes cues with identical start ms, end ms and lines
     * @return $this
     */
    public function removeDuplicateCues()
    {
        // Filtered based on __toString value, reset keys using array_values
        $this->cues = array_values(
            array_unique($this->cues)
        );

        return $this;
    }

    /**
     * Removes all angle brackets from the text lines, then removes empty cues
     * @return $this
     */
    public function stripAngleBracketsFromCues()
    {
        foreach($this->cues as $cue) {
            $cue->alterLines(function($line, $index) {
                return preg_replace('/<.*?>/s', '', $line);
            });
        }

        $this->removeEmptyCues();

        return $this;
    }

    /**
     * Removes all curly brackets and lines containing .ass drawings from the text lines, then removes empty cues
     * @return $this
     */
    public function stripCurlyBracketsFromCues()
    {
        foreach($this->cues as $cue) {
            $cue->alterLines(function($line, $index) {
                // lines containing \p0 or \p1 are drawings from .ass files,
                // they contain no text and should be removed
                if(strpos($line, '\p0') !== false || strpos($line, '\p1') !== false) {
                    return '';
                }

                return preg_replace('/\{.*?\}/s', '', $line);
            });
        }

        $this->removeEmptyCues();

        return $this;
    }

    /**
     * @return GenericSubtitleCue[]
     */
    public function getCues()
    {
        return $this->cues;
    }
}
