<?php

namespace Tests\Unit;

use App\Utils\TextEncoding;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TextEncodingTest extends TestCase
{
    public function test_it_detects_encodings_correctly()
    {
        $fileNamesEncodings = [
            "big5.txt" => "Big5",
            "euc-kr.txt" => "EUC-KR",
            "gb18030.txt" => "gb18030",
         // "gb2312.txt" => "gb2312",
            "iso-8859-7.txt" => "ISO-8859-7",
            "shift-jis.txt" => "Shift_JIS",
            "tis-620.txt" => "TIS-620",
            "ucs-2-le-bom.txt" => "UTF-16",
            "utf-8-bom.txt" => "UTF-8",
            "utf-8.txt" => "UTF-8",
            "windows-1251.txt" => "windows-1251",
            "windows-1252-ansi.txt" => "windows-1252",
            "x-mac-cyrillic.txt" => "MacCyrillic",
        ];

        $textEncoding = app(TextEncoding::class);

        foreach($fileNamesEncodings as $fileName => $expectedEncoding) {
            $filePath = base_path("tests/Storage/TextEncodings/{$fileName}");

            $this->assertSame($expectedEncoding, $textEncoding->detect($filePath));
        }
    }

    public function test_available_encodings()
    {
        $textEncoding = app(TextEncoding::class);

        $availableEncodings = $textEncoding->getAvailableEncodings();

        $this->assertContains("UTF-8", $availableEncodings);
        $this->assertContains("Big5", $availableEncodings);

        // internally used utf-8 bom
        $this->assertContains("UTF-8 BOM", $availableEncodings);

        // ascii/unknown encoding should not exist
        $this->assertNotContains("ascii/unknown", $availableEncodings);
    }

}
