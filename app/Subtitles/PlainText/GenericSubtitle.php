<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\ContainsGenericCues;

class GenericSubtitle
{
    use ContainsGenericCues;

    protected $originalFileNameWithoutExtension = "";

    protected $filePath = "";

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
}
