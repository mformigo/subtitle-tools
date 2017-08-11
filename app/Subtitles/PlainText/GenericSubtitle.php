<?php

namespace App\Subtitles\PlainText;

class GenericSubtitle
{
    protected $originalFileNameWithoutExtension = "";

    protected $filePath = "";

    /**
     * @var GenericSubtitleCue[]
     */
    protected $cues = [];

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function setFileNameWithoutExtension($fileNameWithoutExtension)
    {
        $this->originalFileNameWithoutExtension = $fileNameWithoutExtension;

        return $this;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getFileNameWithoutExtension()
    {
        return $this->originalFileNameWithoutExtension;
    }

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
     * @return GenericSubtitleCue[]
     */
    public function getCues()
    {
        return $this->cues;
    }
}
