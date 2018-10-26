<?php

namespace Tests\Unit\Support\TextFile;

use Exception;
use App\Support\TextFile\Exceptions\TextEncodingException;
use App\Support\TextFile\TextEncoding;
use Tests\TestCase;

class TextEncodingTest extends TestCase
{
    private $fileNamesEncodings = [
        'big5.txt'           => 'Big5',
        'euc-jp.txt'         => 'EUC-JP',
        'euc-kr.txt'         => 'EUC-KR',
        'gb18030.txt'        => 'gb18030',
        'ibm855.txt'         => 'IBM855',
        'ibm866.txt'         => 'IBM866',
        'iso-2022-jp.txt'    => 'ISO-2022-JP',
        'iso-8859-2.txt'     => 'ISO-8859-2',
        'iso-8859-5.txt'     => 'ISO-8859-5',
        'iso-8859-7.txt'     => 'ISO-8859-7',
        'KOI8-R.txt'         => 'KOI8-R',
        'shift-jis.txt'      => 'Shift_JIS',
        'tis-620.txt'        => 'TIS-620',
        'ucs-2-le-bom.txt'   => 'UTF-16',
        'utf-8-bom.txt'      => 'UTF-8',
        'utf-8.txt'          => 'UTF-8',
        'windows-1250.txt'   => 'windows-1250',
        'windows-1251.txt'   => 'windows-1251',
        'windows-1252.txt'   => 'windows-1252',
        'windows-1253.txt'   => 'windows-1253',
        'windows-1254.txt'   => 'windows-1254',
        'windows-1255.txt'   => 'windows-1255',
        'x-mac-cyrillic.txt' => 'MacCyrillic',
        'x-euc-tw.txt'       => 'EUC-TW',
    ];

    /** @test */
    function it_detects_encodings_from_files()
    {
        $textEncoding = new TextEncoding();

        foreach ($this->fileNamesEncodings as $fileName => $expectedEncoding) {
            $this->assertSame(
                $expectedEncoding,
                $textEncoding->detectFromFile($this->testFilesStoragePath.'text-file-package/encodings/'.$fileName)
            );
        }
    }

    /** @test */
    function it_detects_encodings_from_strings()
    {
        $textEncoding = new TextEncoding();

        foreach ($this->fileNamesEncodings as $fileName => $expectedEncoding) {
            $string = file_get_contents($this->testFilesStoragePath.'text-file-package/encodings/'.$fileName);

            $this->assertSame(
                $expectedEncoding,
                $textEncoding->detect($string)
            );
        }
    }

    /** @test */
    function it_can_convert_encodings_to_utf8()
    {
        $textEncoding = new TextEncoding();

        foreach ($this->fileNamesEncodings as $fileName => $expectedEncoding) {
            $string = file_get_contents($this->testFilesStoragePath.'text-file-package/encodings/'.$fileName);

            try {
                $output = $textEncoding->toUtf8($string);
            } catch (Exception $exception) {
                $this->fail("Could not convert '{$expectedEncoding}' to utf8.\n{$exception->getMessage()}");

                return;
            }

            $this->assertNotEmpty($output);
        }
    }

    /** @test */
    function it_ignores_illegal_characters_when_using_iconv()
    {
        $textEncoding = new TextEncoding();

        $string = file_get_contents($this->testFilesStoragePath.'text-file-package/encodings/other/iconv-illegal-chars.txt');

        $output = $textEncoding->toUtf8($string);

        $this->assertTrue(strlen($output) > 10);
    }

    /** @test */
    function it_detects_windows_1250_for_polish_text()
    {
        $textEncoding = new TextEncoding();

        $filePath = $this->testFilesStoragePath.'text-file-package/encodings/1252/windows-1250--with-polish.txt';

        $this->assertSame('windows-1250', $textEncoding->detectFromFile($filePath));
    }

    /** @test */
    function it_detects_windows_1252_for_danish_text()
    {
        $textEncoding = new TextEncoding();

        $filePath = $this->testFilesStoragePath.'text-file-package/encodings/1252/windows-1252--with-danish.txt';

        $this->assertSame('windows-1252', $textEncoding->detectFromFile($filePath));
    }

    /** @test */
    function it_detects_encoding_for_romanian_text()
    {
        $path = $this->testFilesStoragePath.'text-file-package/encodings/iso-8859-2/';

        $this->assertEncoding('ISO-8859-2', $path.'iso-8859-2-000.txt');
        $this->assertEncoding('ISO-8859-2', $path.'iso-8859-2-001.txt');
        $this->assertEncoding('ISO-8859-2', $path.'iso-8859-2-002.txt');
        $this->assertEncoding('ISO-8859-2', $path.'iso-8859-2-003.txt');
        $this->assertEncoding('ISO-8859-2', $path.'iso-8859-2-004.txt');
    }

    /** @test */
    function it_detects_encoding_for_persian_text()
    {
        $path = $this->testFilesStoragePath.'text-file-package/encodings/1256/';

        $this->assertEncoding('windows-1256', $path.'windows-1256--1.txt');
        $this->assertEncoding('windows-1256', $path.'windows-1256--2.txt');
        $this->assertEncoding('windows-1256', $path.'windows-1256--3.txt');
        $this->assertEncoding('windows-1256', $path.'windows-1256--4.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1254_turkish_text()
    {
        $path = $this->testFilesStoragePath.'text-file-package/encodings/1254/';

        $this->assertEncoding('windows-1254', $path.'windows-1254-002.txt');
        $this->assertEncoding('windows-1254', $path.'windows-1254-003.txt');
        $this->assertEncoding('windows-1254', $path.'windows-1254-004.txt');
        $this->assertEncoding('windows-1254', $path.'windows-1254-005.txt');
        $this->assertEncoding('windows-1254', $path.'windows-1254-006.txt');
        $this->assertEncoding('windows-1254', $path.'windows-1254-007.txt');
    }

    /** @test */
    function it_can_convert_mac_cyrillic_to_utf8()
    {
        $textEncoding = new TextEncoding();

        $filePath = $this->testFilesStoragePath.'text-file-package/encodings/x-mac-cyrillic.txt';

        $string = file_get_contents($filePath);

        $encoding = $textEncoding->detectFromFile($filePath);

        $this->assertSame('MacCyrillic', $encoding);

        $utf8Content = $textEncoding->toUtf8($string, $encoding);

        $this->assertContains('Благодаря отсутствию псевдографики и «верхних» управляющих', $utf8Content);
    }

    /** @test */
    function it_throws_a_text_encoding_exception_when_it_cant_detect_the_encoding()
    {
        $this->expectException(TextEncodingException::class);

        $mockTextEncoding = new class extends TextEncoding {
            protected $allowedEncodings = [];
        };

        $mockTextEncoding->detectFromFile($this->testFilesStoragePath.'text-file-package/encodings/utf-8.txt');
    }

    /** @test */
    function it_uses_a_fallback_if_specified_when_it_cant_detect_the_encoding()
    {
        $mockTextEncoding = new class('fallback-encoding') extends TextEncoding {
            protected $allowedEncodings = [];
        };

        $detectedEncoding = $mockTextEncoding->detectFromFile($this->testFilesStoragePath.'text-file-package/encodings/utf-8.txt');

        $this->assertSame('fallback-encoding', $detectedEncoding);
    }

    private function assertEncoding($expected, $filePath)
    {
        $this->assertSame(
            $expected,
            (new TextEncoding)->detectFromFile($filePath),
            'File: '.substr($filePath, strlen($this->testFilesStoragePath))
        );
    }
}
