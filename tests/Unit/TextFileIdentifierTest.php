<?php

namespace Tests\Unit;

use Tests\TestCase;

class TextFileIdentifierTest extends TestCase
{
    /** @test */
    function it_identifies_simple_text_files()
    {
        $identifier = app('TextFileIdentifier');

        $this->assertTrue($identifier->isTextFile("{$this->testFilesStoragePath}TextEncodings/big5.txt"));
    }

    /** @test */
    function it_identifies_empty_files()
    {
        $identifier = app('TextFileIdentifier');

        $this->assertTrue($identifier->isTextFile("{$this->testFilesStoragePath}TextFiles/empty.srt"));
    }

    /** @test */
    function it_identifies_text_files_with_control_characters()
    {
        $identifier = app('TextFileIdentifier');

        $files = [
            "{$this->testFilesStoragePath}TextFiles/mime-octet-mb-chinese.ass",
            "{$this->testFilesStoragePath}TextFiles/mime-octet-utf8.ass",
            "{$this->testFilesStoragePath}TextFiles/mime-octet-utf16.ass",
        ];

        foreach($files as $filePath) {
            $this->assertSame("application/octet-stream", file_mime($filePath));

            $this->assertTrue($identifier->isTextFile($filePath));
        }
    }

    /** @test */
    function it_does_not_identify_when_there_are_too_many_control_chars()
    {
        $identifier = app('TextFileIdentifier');

        $filePath = "{$this->testFilesStoragePath}TextFiles/Fake/dat.ass";

        $this->assertSame("application/octet-stream", file_mime($filePath));

        $this->assertFalse($identifier->isTextFile($filePath));
    }

    /** @test */
    function it_rejects_binary_files()
    {
        $identifier = app('TextFileIdentifier');

        $files = [
            "{$this->testFilesStoragePath}TextFiles/Fake/exe.srt",
            "{$this->testFilesStoragePath}TextFiles/Fake/gif.ass",
            "{$this->testFilesStoragePath}TextFiles/Fake/image.jpg",
            "{$this->testFilesStoragePath}TextFiles/Fake/torrent.srt",
        ];

        foreach($files as $filePath) {
            $this->assertFalse($identifier->isTextFile($filePath));
        }
    }
}
