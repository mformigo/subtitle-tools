<?php

namespace App\Support\Archive\Tests;

use App\Support\Archive\Archive;
use App\Support\Archive\Read\ZipArchiveRead;
use Tests\TestCase;

class ZipArchiveReadTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        if (! ZipArchiveRead::isAvailable()) {
            $this->markTestSkipped('ZipArchive is not available');
        }
    }

    /** @test */
    function it_reads_files()
    {
        $archive = Archive::open($this->testFilesStoragePath.'archive-package/zip/five-text-files.zip');

        $compressedFiles = $archive->getCompressedFiles();

        $this->assertSame(5, count($compressedFiles));
    }

    /** @test */
    function it_reads_files_in_directories()
    {
        $archive = Archive::open($this->testFilesStoragePath.'archive-package/zip/eight-text-files-in-directories.zip');

        $compressedFiles = $archive->getCompressedFiles();

        $this->assertSame(8, count($compressedFiles));
    }

    /** @test */
    function it_does_not_return_directories_as_a_compressed_file()
    {
        $archive = Archive::open($this->testFilesStoragePath.'archive-package/zip/one-empty-directory.zip');

        $this->assertSame(1, $archive->getEntriesCount());

        $compressedFiles = $archive->getCompressedFiles();

        $this->assertSame(0, count($compressedFiles));
    }

    /** @test */
    function it_reads_empty_zips()
    {
        $read = Archive::open($this->testFilesStoragePath.'archive-package/zip/empty.zip');

        $this->assertSame(0, $read->getEntriesCount());
    }

    /** @test */
    function it_extracts_files()
    {
        $archive = Archive::open($this->testFilesStoragePath.'archive-package/zip/five-text-files.zip');

        $compressedFiles = $archive->getCompressedFiles();

        $filePath = $archive->extractFile($compressedFiles[0], storage_disk_file_path('/temporary-files/'));

        $this->assertFileExists($filePath);
    }
}
