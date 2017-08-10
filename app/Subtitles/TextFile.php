<?php

namespace App\Subtitles;

use Illuminate\Http\UploadedFile;

abstract class TextFile
{
    protected $originalFileNameWithoutExtension = "default";

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
    public function loadFile($file)
    {
        if($file instanceof UploadedFile) {
            $this->originalFileNameWithoutExtension = $file->getClientOriginalName();

            $this->filePath = $file->getRealPath();
        }
        else {
            $this->originalFileNameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);

            $this->filePath = $file;
        }

        // These properties come from the WithFileContent/WithFileLines trait
        if(property_exists($this, 'lines')) {
            $this->lines = app('TextFileReader')->getLines($this->filePath);
        }
        else {
            $this->content = app('TextFileReader')->getContents($this->filePath);
        }

        return $this;
    }
}
