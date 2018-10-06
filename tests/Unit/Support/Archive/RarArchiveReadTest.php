<?php

namespace App\Support\Archive\Tests;

use App\Support\Archive\Archive;
use App\Support\Archive\Read\RarArchiveRead;
use Tests\TestCase;

class RarArchiveReadTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        if (! RarArchiveRead::isAvailable()) {
            $this->markTestSkipped('RarArchive is not available');
        }
    }

    /** @test */
    function it_reads_files()
    {
        $archive = Archive::open($this->testFilesStoragePath.'archive-package/rar/ten-text-files.rar');

        $compressedFiles = $archive->getCompressedFiles();

        $this->assertSame(10, count($compressedFiles));
    }

    /** @test */
    function it_reads_files_in_directories()
    {
        $archive = Archive::open($this->testFilesStoragePath.'archive-package/rar/directory-with-three-files.rar');

        $compressedFiles = $archive->getCompressedFiles();

        $this->assertSame(3, count($compressedFiles));
    }

    /** @test */
    function it_does_not_return_directories_as_a_compressed_file()
    {
        $archive = Archive::open($this->testFilesStoragePath.'archive-package/rar/one-empty-directory.rar');

        $this->assertSame(1, $archive->getEntriesCount());

        $compressedFiles = $archive->getCompressedFiles();

        $this->assertSame(0, count($compressedFiles));
    }

    /** @test */
    function it_reads_empty_rar_files()
    {
        $archive = Archive::open($this->testFilesStoragePath.'archive-package/rar/empty.rar');

        $this->assertSame(0, $archive->getEntriesCount());
    }

    /** @test */
    function it_extracts_files()
    {
        $archive = Archive::open($this->testFilesStoragePath.'archive-package/rar/ten-text-files.rar');

        $compressedFiles = $archive->getCompressedFiles();

        $filePath = $archive->extractFile($compressedFiles[0], storage_disk_file_path('/temporary-files/'));

        $this->assertFileExists($filePath);
    }

    /** @test */
    function it_identifies_rar_files()
    {
        $isReadable = Archive::isReadable($this->testFilesStoragePath.'archive-package/rar/ten-text-files.rar');

        $this->assertTrue($isReadable);
    }

    /** @test */
    function it_identifies_empty_rar_files()
    {
        $isReadable = Archive::isReadable($this->testFilesStoragePath.'archive-package/rar/empty.rar');

        $this->assertTrue($isReadable);
    }

    /** @test */
    function it_rejects_split_rar_files()
    {
        $this->assertFalse(Archive::isReadable($this->testFilesStoragePath.'archive-package/rar/split-file.rar'));

        $this->assertFalse(Archive::isReadable($this->testFilesStoragePath.'archive-package/rar/split-file.r01'));
    }
}
