<?php

namespace Tests\Unit;

use App\StoredFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class StoredFileTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function it_stores_files_on_disk_and_in_the_database()
    {
        $originalFilePath = "{$this->testFilesStoragePath}TextFiles/three-cues.ass";

        $storedFile = StoredFile::getOrCreate($originalFilePath);

        $this->assertTrue(StoredFile::count() === 1);

        $this->assertTrue(file_exists($storedFile->filePath));

        $this->assertTrue($originalFilePath !== $storedFile->filePath);

        $this->assertFalse(empty($storedFile->hash));
    }

    /** @test */
    function it_copies_the_input_file()
    {
        $originalFilePath = "{$this->testFilesStoragePath}TextFiles/three-cues.ass";

        $storedFile = StoredFile::getOrCreate($originalFilePath);

        $this->assertTrue(file_exists($originalFilePath));
    }

    /** @test */
    function it_reuses_identical_files()
    {
        $originalFilePath = "{$this->testFilesStoragePath}TextFiles/three-cues.ass";

        $first = StoredFile::getOrCreate($originalFilePath);

        $second = StoredFile::getOrCreate($originalFilePath);

        $this->assertTrue(StoredFile::count() === 1);
    }
}
