<?php

namespace App\Subtitles\PlainText;

class GenericSubtitle
{
    protected $originalFileNameWithoutExtension = "";

    protected $filePath = "";

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

    /**
     * @return GenericSubtitleCue[]
     */
    public function getCues()
    {
        return $this->cues;
    }
}
