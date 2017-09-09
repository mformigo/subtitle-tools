<?php

namespace Tests\Unit;

use App\Utils\Archive\Archive;
use Tests\TestCase;

class RarArchiveReadTest extends TestCase
{
    /** @test */
    function it_reads_files()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/Rar/zimuku-10-ass.rar");

        $this->assertSame(10, count($read->getFiles()));
    }

    /** @test */
    function it_reads_files_in_directories()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/Rar/dir-with-invalid-files.rar");

        $this->assertSame(3, count($read->getFiles()));
    }

    /** @test */
    function it_does_not_read_directories()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/Rar/one-empty-dir.rar");

        $this->assertSame(1, $read->getEntriesCount());

        $this->assertSame(0, count($read->getFiles()));
    }

    /** @test */
    function it_reads_empty_zips()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/Rar/empty.rar");

        $this->assertSame(0, $read->getEntriesCount());
    }

    /** @test */
    function it_extracts_files()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/Rar/zimuku-10-ass.rar");

        $compressedFiles = $read->getFiles();

        $filePath = $read->extractFile($compressedFiles[0]);

        $this->assertTrue(file_exists($filePath));
    }
}
