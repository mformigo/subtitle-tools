<?php

namespace Tests\Unit;

use App\Utils\Archive\Archive;
use Tests\TestCase;

class ZipArchiveReadTest extends TestCase
{
    /** @test */
    function it_reads_files()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/5-text-files-4-good.zip");

        $this->assertSame(5, count($read->getFiles()));
    }

    /** @test */
    function it_reads_files_in_directories()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/dirs-with-ass.zip");

        $this->assertSame(8, count($read->getFiles()));
    }

    /** @test */
    function it_does_not_read_directories()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/one-empty-dir.zip");

        $this->assertSame(1, $read->getEntriesCount());

        $this->assertSame(0, count($read->getFiles()));
    }

    /** @test */
    function it_reads_empty_zips()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/empty.zip");

        $this->assertSame(0, $read->getEntriesCount());
    }

    /** @test */
    function it_extracts_files()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/5-text-files-4-good.zip");

        $compressedFiles = $read->getFiles();

        $filePath = $read->extractFile($compressedFiles[0]);

        $this->assertTrue(file_exists($filePath));
    }
}
