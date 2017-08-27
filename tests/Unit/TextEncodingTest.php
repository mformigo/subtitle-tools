<?php

namespace Tests\Unit;

use Tests\TestCase;

class TextEncodingTest extends TestCase
{
    private $fileNamesEncodings = [
        'big5.txt'       => 'Big5',
        'euc-kr.txt'     => 'EUC-KR',
        'gb18030.txt'    => 'gb18030',
        'iso-8859-7.txt' => 'ISO-8859-7',
        'shift-jis.txt'  => 'Shift_JIS',
        'tis-620.txt'    => 'TIS-620',
        'ucs-2-le-bom.txt'      => 'UTF-16',
        'utf-8-bom.txt'         => 'UTF-8',
        'utf-8.txt'             => 'UTF-8',
        'windows-1251.txt'      => 'windows-1251',
        'windows-1252-ansi.txt' => 'windows-1252',
        'x-mac-cyrillic.txt'    => 'MacCyrillic',
        'KOI8-R.txt'     => 'KOI8-R',
        'iso-8859-5.txt' => 'ISO-8859-5',
    ];

    /** @test */
    function it_detects_encodings_from_files_correctly()
    {
        $textEncoding = app('TextEncoding');

        foreach($this->fileNamesEncodings as $fileName => $expectedEncoding) {
            $filePath = "{$this->testFilesStoragePath}TextEncodings/{$fileName}";

            $this->assertSame($expectedEncoding, $textEncoding->detectFromFile($filePath));
        }
    }

    /** @test */
    function it_detects_encodings_from_strings_correctly()
    {
        $textEncoding = app('TextEncoding');

        foreach($this->fileNamesEncodings as $fileName => $expectedEncoding) {
            $filePath = "{$this->testFilesStoragePath}TextEncodings/{$fileName}";

            $string = file_get_contents($filePath);

            $this->assertSame($expectedEncoding, $textEncoding->detect($string));
        }
    }
}
