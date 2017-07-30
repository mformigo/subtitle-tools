<?php

namespace App\Utils;

class TextFileReader
{
    private $textFileIdentifier;
    private $textEncoding;

    public function __construct(TextFileIdentifier $identifier, TextEncoding $textEncoding)
    {
        $this->textFileIdentifier = $identifier;

        $this->textEncoding = $textEncoding;
    }

    public function getContents($filePath)
    {
        if(!file_exists($filePath)) {
            throw new \Exception("File does not exist ({$filePath})");
        }

        if(!$this->textFileIdentifier->isTextFile($filePath)) {
            throw new \Exception("File is not a text file ({$filePath})");
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new \Exception("Failed reading file ({$filePath})");
        }

        return $this->textEncoding->toUtf8($content);
    }

    public function getLines($filePath)
    {
        return preg_split("/\r\n|\n|\r/", $this->getContents($filePath));
    }

}