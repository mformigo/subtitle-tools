<?php

namespace Tests\Unit;

use Tests\TestCase;


class TextFileReaderTest extends TestCase
{
    function test_it_reads_simple_text_files()
    {
        $reader = app('TextFileReader');

        $filePath = base_path("tests/Storage/TextEncodings/big5.txt");

        $content = $reader->getContents($filePath);
        $lines = $reader->getLines($filePath);

        $this->assertSame(104088, strlen($content));
        $this->assertSame(6642, count($lines));
    }

    function test_it_reads_empty_files()
    {
        $reader = app('TextFileReader');

        $filePath = base_path("tests/Storage/TextFiles/empty.srt");

        $content = $reader->getContents($filePath);
        $lines = $reader->getLines($filePath);

        $this->assertSame("", $content);
        $this->assertSame([""], $lines);
    }

    function test_it_reads_text_files_with_control_characters()
    {
        $reader = app('TextFileReader');

        $content = $reader->getContents(base_path("tests/Storage/TextFiles/mime-octet-mb-chinese.ass"));

        $this->assertSame(821, strlen($content));
    }

}
