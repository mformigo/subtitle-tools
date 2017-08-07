<?php

namespace Tests\Unit;

use Tests\TestCase;


class TextFileIdentifierTest extends TestCase
{
    function test_it_identifies_simple_text_files()
    {
        $identifier = app('TextFileIdentifier');

        $this->assertTrue($identifier->isTextFile(base_path("tests/Storage/TextEncodings/big5.txt")));
    }

    function test_it_identifies_empty_files()
    {
        $identifier = app('TextFileIdentifier');

        $this->assertTrue($identifier->isTextFile(base_path("tests/Storage/TextFiles/empty.srt")));
    }

    function test_it_identifies_text_files_with_control_characters()
    {
        $identifier = app('TextFileIdentifier');

        $files = [
            base_path("tests/Storage/TextFiles/mime-octet-mb-chinese.ass"),
            base_path("tests/Storage/TextFiles/mime-octet-utf8.ass"),
            base_path("tests/Storage/TextFiles/mime-octet-utf16.ass"),
        ];

        foreach($files as $filePath) {
            $this->assertSame("application/octet-stream", file_mime($filePath));

            $this->assertTrue($identifier->isTextFile($filePath));
        }
    }

    function test_it_does_not_identify_when_there_are_too_many_control_chars()
    {
        $identifier = app('TextFileIdentifier');

        $filePath = base_path("tests/Storage/TextFiles/Fake/dat.ass");

        $this->assertSame("application/octet-stream", file_mime($filePath));

        $this->assertFalse($identifier->isTextFile($filePath));
    }

    function test_it_rejects_binary_files()
    {
        $identifier = app('TextFileIdentifier');

        $files = [
            base_path("tests/Storage/TextFiles/Fake/exe.srt"),
            base_path("tests/Storage/TextFiles/Fake/gif.ass"),
            base_path("tests/Storage/TextFiles/Fake/image.jpg"),
            base_path("tests/Storage/TextFiles/Fake/torrent.srt"),
        ];

        foreach($files as $filePath) {
            $this->assertFalse($identifier->isTextFile($filePath));
        }
    }

}
