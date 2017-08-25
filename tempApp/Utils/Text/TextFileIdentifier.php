<?php

namespace App\Utils\Text;

class TextFileIdentifier
{
    private $textEncoding;

    public function __construct(TextEncoding $textEncoding)
    {
        $this->textEncoding = $textEncoding;
    }

    public function isTextFile($filePath)
    {
        if(!file_exists($filePath)) {
            throw new \Exception("File does not exist ({$filePath})");
        }

        // Empty files can safely be treated as text files
        if(filesize($filePath) === 0) {
            return true;
        }

        $mimeType = file_mime($filePath);

        if(starts_with($mimeType, "text/")) {
            return true;
        }

        // at this point, mimes that are not octet-stream are definitely not text files
        if($mimeType !== "application/octet-stream") {
            return false;
        }

        $fromEncoding = $this->textEncoding->detectFromFile($filePath);

        $handle = fopen($filePath, 'rb');

        while (!feof($handle)) {
            $chunk = fread($handle, 4096);
            $controlCharCount = 0;

            if(strlen($chunk) === 0) {
                continue;
            }

            $chunk = $this->textEncoding->toUtf8($chunk, $fromEncoding);

            // significant speed improvement when only evaluating this once
            $mbStrLength = mb_strlen($chunk);

            for($i = 0; $i < $mbStrLength; $i++) {
                if($chunk[$i] !== "\r" && $chunk[$i] !== "\n" && ctype_cntrl($chunk[$i])) {
                    $controlCharCount++;
                }
            }

            $validTextPercentage = round(100 - $controlCharCount / $mbStrLength * 100, 3);

            if($validTextPercentage < 99.0) {
                return false;
            }

        }

        return true;
    }
}
