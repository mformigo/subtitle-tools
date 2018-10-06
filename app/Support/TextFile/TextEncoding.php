<?php

namespace App\Support\TextFile;

use RuntimeException;
use App\Support\TextFile\Exceptions\TextEncodingException;

class TextEncoding
{
    protected $fallbackEncoding = null;

    protected $allowedEncodings = [
        //  uchardet name   php encoding name
        'ascii/unknown'  => 'UTF-8', // assuming UTF-8
        'Big5'           => 'Big5', // Traditional Chinese
        'EUC-JP'         => 'EUC-JP',
        'EUC-KR'         => 'EUC-KR', // Korean
        'gb18030'        => 'gb18030', // Simplified Chinese
        'gb2312'         => 'gb2312', // Simplified Chinese
        'IBM855'         => 'IBM855',
        'IBM866'         => 'IBM866',
        'ISO-2022-JP'    => 'ISO-2022-JP',
        'ISO-8859-2'     => 'ISO-8859-2', // Romanian (gets detected as windows-1252)
        'ISO-8859-5'     => 'ISO-8859-5',
        'ISO-8859-7'     => 'ISO-8859-7',
        'ISO-8859-8'     => 'ISO-8859-8',
        'KOI8-R'         => 'KOI8-R',
        'Shift_JIS'      => 'Shift_JIS', // Japanese
        'TIS-620'        => 'TIS-620', // Thai
        'UTF-16'         => 'UTF-16',
        'UTF-8'          => 'UTF-8',
        'windows-1250'   => 'windows-1250', // ANSI (for Polish, doesn't work for scandinavian languages)
        'windows-1251'   => 'windows-1251', // Russian
        'windows-1252'   => 'windows-1252', // ANSI (for scandinavian languages, doesn't work for Polish)
        'windows-1253'   => 'windows-1253',
        'windows-1255'   => 'windows-1255',
        'windows-1256'   => 'windows-1256',
        'x-euc-tw'       => 'EUC-TW',
        'x-mac-cyrillic' => 'MacCyrillic',
    ];

    protected $iconvEncodings = [
    //  php encoding name
        'IBM855',
        'MacCyrillic',
        'TIS-620',
        'windows-1250',
        'windows-1253',
        'windows-1255',
        'windows-1256',
    ];

    public function __construct($fallbackEncoding = null)
    {
        $this->fallbackEncoding = $fallbackEncoding;
    }

    public function detect($string)
    {
        $tempFileHandle = tmpfile();

        fwrite($tempFileHandle, $string);

        return $this->detectFromFile(stream_get_meta_data($tempFileHandle)['uri']);
    }

    public function detectFromFile($filePath)
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException('File does not exist :'.$filePath);
        }

        $encoding = trim(
            shell_exec("uchardet \"{$filePath}\"")
        );

        if (empty($encoding)) {
            return $this->allowedEncodings['ascii/unknown'];
        }

        if (! $this->isAllowedEncoding($encoding)) {
            if ($this->fallbackEncoding) {
                return $this->fallbackEncoding;
            }

            throw new TextEncodingException("Detected: {$encoding}, was not on the whitelist");
        }

        $encodingName = $this->allowedEncodings[$encoding];

        // When uchardet detects windows-1252, some manual work is needed to
        // figure out the correct encoding
        if ($encodingName === 'windows-1252') {
            $encodingName = $this->getCorrect1252Encoding($filePath);
        }

        if ($encodingName === 'MacCyrillic') {
            $encodingName = $this->getCorrectMacCyrillicEncoding($filePath);
        }

        return $encodingName;
    }

    public function toUtf8($string, $inputEncoding = null): string
    {
        return $this->to($string, 'UTF-8', $inputEncoding);
    }

    protected function to($string, $outputEncoding, $inputEncoding = null): string
    {
        $inputEncoding = $inputEncoding ?? $this->detect($string);

        // Remove the BOM from files encoded in utf-8
        if (stripos($inputEncoding, 'utf-8') === 0) {
            $utf8Bom = pack('H*', 'EFBBBF');

            if (preg_match("/^{$utf8Bom}/", $string)) {
                $string = preg_replace("/^{$utf8Bom}/", '', $string);
            }
        }

        if ($this->isIconvEncoding($inputEncoding)) {
            return iconv($inputEncoding, "{$outputEncoding}//IGNORE", $string);
        }

        return mb_convert_encoding($string, $outputEncoding, $inputEncoding);
    }

    protected function isAllowedEncoding($encoding)
    {
        return isset($this->allowedEncodings[$encoding]);
    }

    protected function isIconvEncoding($encoding)
    {
        return in_array($encoding, $this->iconvEncodings);
    }

    protected function getCorrect1252Encoding($filePath)
    {
        $content = file_get_contents($filePath);

        if (strpos($content, "\xB3") !== false) {
            // B3 hex in windows-1252 === ³ (cube)
            // B3 hex in windows-1250 === ł (polish letter)
            return 'windows-1250';
        } elseif (strpos($content, "\xBA") !== false) {
            // BA hex in windows-1252 === º (degree sign)
            // BA hex in   ISO-8859-2 === ş (romanian letter)
            return 'ISO-8859-2';
        }

        return 'windows-1252';
    }

    protected function getCorrectMacCyrillicEncoding($filePath)
    {
        $content = file_get_contents($filePath);

        if (substr_count($content, "\xA1") > 2) {
            // A1 hex in windows-1256 === ﺧ (something Persian)
            // A1 hex in MacCyrillic  === ° (degree sign)
            return 'windows-1256';
        }

        $windows1256Content = $this->toUtf8($content, 'windows-1256');

        // \xA7
        if (strpos($windows1256Content, 'ﺱ') !== false) {
            return 'windows-1256';
        }

        return 'MacCyrillic';
    }
}
