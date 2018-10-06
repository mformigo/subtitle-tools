<?php

namespace App\Support\TextFile;

use RuntimeException;

class TextFileReader
{
    protected $textFileIdentifier;

    protected $textEncoding;

    public function __construct(TextFileIdentifier $identifier = null, TextEncoding $textEncoding = null)
    {
        $this->textFileIdentifier = $identifier ?? new TextFileIdentifier();

        $this->textEncoding = $textEncoding ?? new TextEncoding();
    }

    /**
     * @param $filePath
     *
     * @return string Content of the file in UTF-8
     *
     * @throws RuntimeException
     */
    public function getContent($filePath)
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException('File does not exist: '.$filePath);
        }

        if (! $this->textFileIdentifier->isTextFile($filePath)) {
            throw new RuntimeException('File is not a text file: '.$filePath);
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new RuntimeException('Failed reading file :'.$filePath);
        }

        return $this->textEncoding->toUtf8($content);
    }

    /**
     * @param $filePath
     *
     * @return array|string[] Lines of the file in UTF-8
     */
    public function getLines($filePath)
    {
        return preg_split("/\r\n|\n|\r/", $this->getContent($filePath));
    }
}
