<?php

namespace Tests\Unit;

use App\Utils\Archive\Archive;
use App\Utils\Archive\Read\ArchiveReadInterface;
use Tests\TestCase;

class ArchiveTest extends TestCase
{
    /** @test */
    function it_returns_archive_read()
    {
        $read = Archive::read("{$this->testFilesStoragePath}Archives/empty.zip");

        $this->assertTrue($read instanceof ArchiveReadInterface);
    }

    /** @test */
    function it_identifies_zip_files_strict()
    {
        $this->assertTrue(Archive::isArchive("{$this->testFilesStoragePath}Archives/5-text-files-4-good.zip", true));

        $this->assertFalse(Archive::isArchive("{$this->testFilesStoragePath}TextFiles/three-cues.ass", true));
    }

    /** @test */
    function it_identifies_zip_files_not_strict()
    {
        $this->assertTrue(Archive::isArchive("{$this->testFilesStoragePath}Archives/5-text-files-4-good.zip", false));

        $this->assertFalse(Archive::isArchive("{$this->testFilesStoragePath}TextFiles/three-cues.ass", false));
    }

    /** @test */
    function it_identifies_empty_zip_files()
    {
        $this->assertTrue(Archive::isArchive("{$this->testFilesStoragePath}Archives/empty.zip"));
    }

    /** @test */
    function it_identifies_rar_files_strict()
    {
        $this->assertTrue(Archive::isArchive("{$this->testFilesStoragePath}Archives/Rar/zimuku-10-ass.rar", true));
    }

    /** @test */
    function it_identifies_rar_files_not_strict()
    {
        $this->assertTrue(Archive::isArchive("{$this->testFilesStoragePath}Archives/Rar/zimuku-10-ass.rar", false));
    }

    /** @test */
    function it_identifies_empty_rar_files()
    {
        $this->assertTrue(Archive::isArchive("{$this->testFilesStoragePath}Archives/Rar/empty.rar"));
    }

    /** @test */
    function it_rejects_split_rar_files()
    {
        $this->assertFalse(Archive::isArchive("{$this->testFilesStoragePath}Archives/Rar/split-file.rar"));

        $this->assertFalse(Archive::isArchive("{$this->testFilesStoragePath}Archives/Rar/split-file.r01"));
    }
}
