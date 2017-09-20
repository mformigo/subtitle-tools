<?php

namespace App\Subtitles;

use Illuminate\Http\UploadedFile;

abstract class TextFile
{
    protected $originalFileNameWithoutExtension = "default";

    protected $extension = "txt";

    protected $filePath = false;

    /**
     * Returns true if the $filePath file is a valid format for this class
     * @param $file
     * @return bool
     */
    public abstract static function isThisFormat($file);

    public abstract function getContent();

    public abstract function getContentLines();

    public function getExtension()
    {
        return $this->extension;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFileNameWithoutExtension($fileNameWithoutExtension)
    {
        $this->originalFileNameWithoutExtension = $fileNameWithoutExtension;

        return $this;
    }

    public function getFileNameWithoutExtension()
    {
        return $this->originalFileNameWithoutExtension;
    }

    /**
     * @param $file string|UploadedFile A file path or UploadedFile
     * @return $this
     */
    public function loadFile($file)
    {
        $name = $file instanceof UploadedFile ? $file->getClientOriginalName() : $file;

        $this->originalFileNameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);

        $this->filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        // These properties come from the WithFileContent/WithFileLines trait
        if(property_exists($this, 'lines')) {
            $this->lines = array_map('trim', app(\SjorsO\TextFile\Contracts\TextFileReaderInterface::class)->getLines($this->filePath));
        }
        else {
            $this->content = app(\SjorsO\TextFile\Contracts\TextFileReaderInterface::class)->getContent($this->filePath);
        }

        return $this;
    }
}
