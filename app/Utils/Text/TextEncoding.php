<?php

namespace App\Utils\Text;

use App\Utils\TempFile;
use Illuminate\Support\Facades\Storage;

class TextEncoding
{
    private $allowedEncodings = [
    //  uchardet name       php encoding name
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
    //  php encoding name
        "TIS-620",
        "MacCyrillic",
    ];

    public function detectFromFile($filePath)
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

    public function detect($string)
    {
        $tempFilePath = (new TempFile())->make($string);

        $resultBool = $this->detectFromFile($tempFilePath);

        if(file_exists($tempFilePath)) {
            unlink($tempFilePath);
        }

        return $resultBool;
    }

    private function to($string, $outputEncoding, $inputEncoding = null)
    {
        $inputEncoding = $inputEncoding ?? $this->detect($string);

        if(starts_with($inputEncoding, "UTF-8")) {
            $utf8_bom = pack('H*', 'EFBBBF');

            if(preg_match("/^{$utf8_bom}/", $string)) {
                $string = preg_replace("/^{$utf8_bom}/", '', $string);
            }
        }

        if($this->isIconvEncoding($inputEncoding)) {
            return iconv($inputEncoding, $outputEncoding, $string);
        }

        return mb_convert_encoding($string, $outputEncoding, $inputEncoding);
    }

    public function toUtf8($string, $inputEncoding = null)
    {
        return $this->to($string, "UTF-8", $inputEncoding);
    }

    private function isAllowedEncoding($encoding)
    {
        return isset($this->allowedEncodings[$encoding]);
    }

    private function isIconvEncoding($encoding)
    {
        return in_array($encoding, $this->iconvEncodings);
    }

//    public function getAvailableOutputEncodings()
//    {
//        // ascii/unknown is a fallback, and is removed from the array
//        return array_diff(array_keys($this->allowedEncodings), ["ascii/unknown"]);
//    }

}
