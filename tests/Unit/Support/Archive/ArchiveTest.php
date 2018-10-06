<?php

namespace App\Support\Archive\Tests;

use App\Support\Archive\Archive;
use App\Support\Archive\Read\ArchiveRead;
use Tests\TestCase;

class ArchiveTest extends TestCase
{
    /** @test */
    function it_identifies_archive_files()
    {
        $isReadable = Archive::isReadable($this->testFilesStoragePath.'archive-package/zip/five-text-files.zip');

        $this->assertTrue($isReadable);
    }

    /** @test */
    function it_rejects_invalid_zips()
    {
        // This file is not a zip file, but has a .zip extension
        $isReadable = Archive::isReadable($this->testFilesStoragePath.'archive-package/zip/fake.zip');

        $this->assertFalse($isReadable);
    }

    /** @test */
    function it_gets_the_read_interface()
    {
        $archive = Archive::open($this->testFilesStoragePath.'archive-package/zip/five-text-files.zip');

        $this->assertTrue($archive instanceof ArchiveRead);
    }
}
