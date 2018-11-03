<?php

namespace Tests\Components\TextFile;

use Exception;
use App\Support\TextFile\Exceptions\TextEncodingException;
use App\Support\TextFile\TextEncoding;
use Tests\TestCase;

class TextEncodingTest extends TestCase
{
    private $path;

    /** @test */
    function it_detects_encoding_for_windows_1255()
    {
        $this->assertEncoding('windows-1255', 'windows-1255/windows-1255-000-heb.txt');
    }

    /** @test */
    function it_detects_encoding_for_euc_tw()
    {
        // seems like gibberish
        $this->assertEncoding('EUC-TW', 'euc-tw/euc-tw-000.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1251()
    {
        $this->assertEncoding('windows-1251', 'windows-1251/windows-1251-000-bul.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1253()
    {
        // this is a binary file.
        $this->assertEncoding('windows-1253', 'windows-1253/windows-1253-000.txt');
    }

    /** @test */
    function it_detects_encoding_for_utf_8()
    {
        $this->assertEncoding('UTF-8', 'utf-8/utf-8-000-chi.txt');
        $this->assertEncoding('UTF-8', 'utf-8/utf-8-001.txt');
    }

    /** @test */
    function it_detects_encoding_for_utf_16()
    {
        $this->assertEncoding('UTF-16', 'utf-16/utf-16-000-chi.txt');
    }

    /** @test */
    function it_detects_encoding_for_tis_620()
    {
        $this->assertEncoding('TIS-620', 'tis-620/tis-620-000-tha.txt');
    }

    /** @test */
    function it_detects_encoding_for_shift_jis()
    {
        $this->assertEncoding('Shift_JIS', 'shift-jis/shift-jis-000-jpn.txt');
    }

    /** @test */
    function it_detects_encoding_for_koi8_r()
    {
        // this file is mostly NULL bytes
        $this->assertEncoding('KOI8-R', 'koi8-r/koi8-r-000.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_7()
    {
        $this->assertEncoding('ISO-8859-7', 'iso-8859-7/iso-8859-7-000-gre.txt');
    }

    /** @test */
    function it_detects_encoding_for_big5()
    {
        $this->assertEncoding('Big5', 'big5/big5-001-zho.txt');
    }

    /** @test */
    function it_detects_encoding_for_euc_jp()
    {
        $this->assertEncoding('EUC-JP', 'euc-jp/euc-jp-001.txt');
    }

    /** @test */
    function it_detects_encoding_for_euc_kr()
    {
        $this->assertEncoding('EUC-KR', 'euc-kr/euc-kr-001-kor.txt');
    }

    /** @test */
    function it_detects_encoding_for_gb18030()
    {
        $this->assertEncoding('gb18030', 'gb18030/gb18030-001-chi.txt');
    }

    /** @test */
    function it_detects_encoding_for_ibm855()
    {
        $this->assertEncoding('IBM855', 'ibm855/ibm855-001-eng.txt');
    }

    /** @test */
    function it_detects_encoding_for_ibm866()
    {
        $this->assertEncoding('IBM866', 'ibm866/ibm866-001-eng.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_2022_jp()
    {
        $this->assertEncoding('ISO-2022-JP', 'iso-2022-jp/iso-2022-jp-001-jpn.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_2()
    {
        // ron = Romanian
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-000-ron.txt');
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-001-ron.txt');
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-002-ron.txt');
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-003-ron.txt');
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-004-ron.txt');
        $this->assertEncoding('ISO-8859-2', 'iso-8859-2/iso-8859-2-005-ron.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1254()
    {
        $this->assertEncoding('windows-1254', 'windows-1254/windows-1254-000-tur.txt');
        $this->assertEncoding('windows-1254', 'windows-1254/windows-1254-001-tur.txt');
        $this->assertEncoding('windows-1254', 'windows-1254/windows-1254-002-tur.txt');
        $this->assertEncoding('windows-1254', 'windows-1254/windows-1254-003-tur.txt');
        $this->assertEncoding('windows-1254', 'windows-1254/windows-1254-004-tur.txt');
        $this->assertEncoding('windows-1254', 'windows-1254/windows-1254-005-tur.txt');
        $this->assertEncoding('windows-1254', 'windows-1254/windows-1254-006-tur.txt');
        $this->assertEncoding('windows-1254', 'windows-1254/windows-1254-007-tur.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1250()
    {
        $this->assertEncoding('windows-1250', 'windows-1250/windows-1250-000-pol.txt');
        $this->assertEncoding('windows-1250', 'windows-1250/windows-1250-001-pol.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1252()
    {
        $this->assertEncoding('windows-1252', 'windows-1252/windows-1252-000-dan.txt');
        $this->assertEncoding('windows-1252', 'windows-1252/windows-1252-001-eng.txt');
    }

    /** @test */
    function it_detects_encoding_for_windows_1256()
    {
        // fas = per = Persian
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-000-fas.txt');
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-001-fas.txt');
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-002-fas.txt');
        $this->assertEncoding('windows-1256', 'windows-1256/windows-1256-003-fas.txt');
    }

    /** @test */
    function it_detects_encoding_for_mac_cyrillic()
    {
        $this->assertEncoding('MacCyrillic', 'mac-cyrillic/mac-cyrillic-000-rus.txt');
    }

    /** @test */
    function it_detects_encoding_for_iso_8859_5()
    {
        // this is a ".nfo" file
        $this->assertEncoding('ISO-8859-5', 'iso-8859-5/iso-8859-5-000.txt');
    }

    /** @test */
    function it_detects_encoding_for_hz()
    {
        // might be a false-positive
        $this->assertEncoding('HZ', 'hz/hz-000-eng.txt');
    }

    /** @test */
    function it_detects_encodings_from_strings()
    {
        $string = file_get_contents($this->path.'big5/big5-001-zho.txt');

        $this->assertSame(
            'Big5',
            (new TextEncoding)->detect($string)
        );
    }

    /** @test */
    function it_ignores_illegal_characters_when_using_iconv()
    {
        $string = file_get_contents($this->path.'iconv-illegal-chars.txt');

        $output = (new TextEncoding)->toUtf8($string);

        $this->assertTrue(strlen($output) > 10);
    }

    /** @test */
    function it_throws_a_text_encoding_exception_when_it_cant_detect_the_encoding()
    {
        $this->expectException(TextEncodingException::class);

        $mockTextEncoding = new class extends TextEncoding {
            protected $allowedEncodings = [];
        };

        $mockTextEncoding->detectFromFile($this->path.'big5/big5-001-zho.txt');
    }

    /** @test */
    function it_uses_a_fallback_if_specified_when_it_cant_detect_the_encoding()
    {
        $mock = new class('fallback-encoding') extends TextEncoding {
            protected $allowedEncodings = [];
        };

        $this->assertSame(
            'fallback-encoding',
            $mock->detectFromFile($this->path.'big5/big5-001-zho.txt')
        );
    }

    private function assertEncoding($expected, $fileName)
    {
        $filePath = $this->path.ltrim($fileName, DIRECTORY_SEPARATOR);

        $textEncoding = new TextEncoding();

        $this->assertSame(
            $expected,
            $textEncoding->detectFromFile($filePath),
            'File: '.$fileName
        );

        $string = file_get_contents($filePath);

        try {
            $output = $textEncoding->toUtf8($string);
        } catch (Exception $exception) {
            $this->fail("Could not convert $expected to UTF-8.\n$fileName\n".$exception->getMessage());

            return;
        }

        $this->assertTrue(strlen($output) > 200);
    }

    public function setUp()
    {
        parent::setUp();

        $this->path = base_path('tests/Components/TextEncoding/Files/');

        $this->testFilesStoragePath = 'dont-use-this-one';
    }
}
