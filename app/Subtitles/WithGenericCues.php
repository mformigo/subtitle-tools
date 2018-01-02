<?php

namespace App\Subtitles;

use App\Subtitles\PlainText\GenericSubtitleCue;

trait WithGenericCues
{
    /**
     * @var GenericSubtitleCue[]
     */
    protected $cues = [];

    public function addCue($cue)
    {
        $this->cues[] = $cue;

        return $this;
    }

    public function hasCues()
    {
        return count($this->cues) > 0;
    }

    /**
     * Remove all cues that do not have text lines
     *
     * @return $this
     */
    public function removeEmptyCues()
    {
        $this->cues = array_filter($this->cues, function (GenericSubtitleCue $cue) {
            return $cue->hasLines();
        });

        // Reset the array keys
        $this->cues = array_values($this->cues);

        return $this;
    }

    /**
     * Removes cues with identical start ms, end ms and lines
     *
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
     * Removes parentheses from the text lines, then removes empty cues
     *
     * @return $this
     */
    public function stripParenthesesFromCues()
    {
        foreach ($this->cues as $cue) {
            $cue->alterAllLines(function ($lines) {
                $singleLine = implode("\n", $lines);

                $strippedLines = preg_replace('/\(.*?\)/s', '', $singleLine);

                return explode("\n", $strippedLines);
            });
        }

        return $this->removeEmptyCues();
    }

    /**
     * Removes all angle brackets from the text lines, then removes empty cues
     *
     * @return $this
     */
    public function stripAngleBracketsFromCues()
    {
        foreach ($this->cues as $cue) {
            $cue->alterAllLines(function ($lines) {
                $singleLine = implode("\n", $lines);

                $strippedLines = preg_replace('/<.*?>/s', '', $singleLine);

                return explode("\n", $strippedLines);
            });
        }

        $this->removeEmptyCues();

        return $this;
    }

    /**
     * Removes all curly brackets and lines containing .ass drawings from the
     * text lines, then removes empty cues
     *
     * @return $this
     */
    public function stripCurlyBracketsFromCues()
    {
        foreach ($this->cues as $cue) {
            $cue->alterAllLines(function ($lines) {
                $singleLine = collect($lines)
                    ->filter(function ($line) {
                        // lines containing \p0 or \p1 are drawings from .ass files,
                        // they contain no text and should be removed
                        return ! str_contains($line, ['\p0', '\p1']);
                    })
                    ->implode("\n");

                $strippedLines = preg_replace('/\{.*?\}/s', '', $singleLine);

                return explode("\n", $strippedLines);
            });
        }

        $this->removeEmptyCues();

        return $this;
    }

    /**
     * @param bool $sortCues
     *
     * @return GenericSubtitleCue[]
     */
    public function getCues($sortCues = true)
    {
        //TODO: sortCues was added because smi parsing uses this function a lot, this should be fixed more neatly
        if ($sortCues) {
            $this->sortCues();
        }

        return $this->cues;
    }

    public function sortCues()
    {
        usort($this->cues, function (GenericSubtitleCue $a, GenericSubtitleCue $b) {
            return $a->getStartMs() <=> $b->getStartMs();
        });

        return $this;
    }
}
