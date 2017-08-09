<?php

namespace App\Subtitles;

use Illuminate\Http\UploadedFile;

abstract class TextFile
{
    protected $originalFileNameWithoutExtension = "text-file";

    protected $extension = ".txt";

    protected $filePath = false;

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

    public abstract function getContent();

    public function getContentLines()
    {
        return preg_split("/\r\n|\n|\r/", $this->getContent());
    }

    /**
     * Returns true if the $filePath file is a valid format for this class
     * @param $file
     * @return bool
     */
    public abstract static function isThisFormat($file);

    /**
     * @param $file string|UploadedFile A file path or UploadedFile
     * @return $this
     */
    public abstract static function createFromFile($file);
}
