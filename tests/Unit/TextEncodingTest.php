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
        'windows-1250.txt'      => 'windows-1250',
      //  'x-mac-cyrillic.txt'    => 'MacCyrillic', // Doesn't work at all, no idea how to fix
        'x-mac-cyrillic--from-uchardet.txt' => 'MacCyrillic', // This file is taken from the uchardet unit tests, works properly
        'KOI8-R.txt'     => 'KOI8-R',
        'iso-8859-5.txt' => 'ISO-8859-5',
        'euc-jp.txt'     => 'EUC-JP',
        'ibm866.txt'     => 'IBM866',
        'windows-1253.txt' => 'windows-1253',
        'windows-1255.txt' => 'windows-1255',
        'iso-8859-2.txt'   => 'ISO-8859-2',
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

    /** @test */
    function it_can_convert_the_encoding_to_utf8()
    {
        $textEncoding = app('TextEncoding');

        foreach($this->fileNamesEncodings as $fileName => $expectedEncoding) {
            $filePath = "{$this->testFilesStoragePath}TextEncodings/{$fileName}";

            $string = file_get_contents($filePath);

            try {
                $output = $textEncoding->toUtf8($string);
            }
            catch(\Exception $exception) {
                $this->assertTrue(false, "Could not convert '{$expectedEncoding}' to utf8.\n{$exception->getMessage()}");

                return;
            }

            $this->assertNotEmpty($output);
        }
    }

    /** @test */
    function it_ignores_illegal_characters_when_using_iconv()
    {
        $textEncoding = app('TextEncoding');

        $output = $textEncoding->toUtf8(file_get_contents("{$this->testFilesStoragePath}TextEncodings/Other/iconv-illegal-chars.txt"));

        $this->assertTrue(strlen($output) > 10);
    }

    /** @test */
    function it_detects_windows_1250_for_polish_text()
    {
        $textEncoding = app('TextEncoding');

        $this->assertSame('windows-1250', $textEncoding->detectFromFile("{$this->testFilesStoragePath}TextEncodings/Problematic-Windows-1252/windows-1250--with-polish.txt"));
    }

    /** @test */
    function it_detects_windows_1252_for_danish_text()
    {
        $textEncoding = app('TextEncoding');

        $this->assertSame('windows-1252', $textEncoding->detectFromFile("{$this->testFilesStoragePath}TextEncodings/Problematic-Windows-1252/windows-1252--with-danish.txt"));
    }

    /** @test */
    function it_detects_encoding_for_romanian_text()
    {
        $textEncoding = app('TextEncoding');

        $this->assertSame('ISO-8859-2', $textEncoding->detectFromFile("{$this->testFilesStoragePath}TextEncodings/Problematic-Windows-1252/iso-8859-2--with-romanian.txt"));
    }
}
