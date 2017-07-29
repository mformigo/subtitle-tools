<?php

namespace App\Utils;

class TextEncoding
{
    private $allowedEncodings = [
    //  uchardet name       php encoding name
        "UTF-8 BOM"      => "UTF-8", // Not a real encoding, but we use it internally
        "UTF-8"          => "UTF-8",
        "ascii/unknown"  => "UTF-8", // We assume UTF-8 here
        "UTF-16"         => "UTF-16",
        "windows-1252"   => "windows-1252", // ANSI
        "Shift_JIS"      => "Shift_JIS", // Japanese
        "Big5"           => "Big5", // Traditional Chinese
        "gb18030"        => "gb18030", // Simplified Chinese
        "gb2312"         => "gb2312", // Simplified Chinese
        "TIS-620"        => "TIS-620", // Thai
        "EUC-KR"         => "EUC-KR", // Korean
        "windows-1251"   => "windows-1251", // Russian
        "x-mac-cyrillic" => "MacCyrillic",
        "ISO-8859-7"     => "ISO-8859-7",
    ];

    private $iconvEncodings = [
        "TIS-620",
        "MacCyrillic",
    ];

    public function detect($filePath)
    {
        if(!file_exists($filePath)) {
            throw new \Exception("File does not exist ({$filePath})");
        }

        // TODO: check cache

        $encoding = trim(shell_exec("uchardet \"{$filePath}\""));

        if(!$this->isAllowedEncoding($encoding)) {
            throw new \Exception("Unable to detect file encoding of {$filePath}");
        }

        $encodingName = $this->allowedEncodings[$encoding];

        // TODO: set cache

        return $encodingName;
    }

    public function to($string, $outputEncoding)
    {
        return $string;
    }

    public function toUtf8($string)
    {
        return $this->to($string, "UTF-8");
    }

    public function getAvailableEncodings()
    {
        // remove ascii/unknown using array_diff
        return array_diff(array_keys($this->allowedEncodings), ["ascii/unknown"]);
    }

    private function isAllowedEncoding($encoding)
    {
        return isset($this->allowedEncodings[$encoding]);
    }

    private function isIconvEncoding($encoding)
    {
        return in_array($encoding, $this->iconvEncodings);
    }

}