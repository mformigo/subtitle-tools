<?php

namespace Tests\Unit;

use App\Support\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoredFileTest extends TestCase
{
    use RefreshDatabase;

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

    /** @test */
    function it_stores_text_files_with_utf8_bom()
    {
        $originalFilePath = "{$this->testFilesStoragePath}TextEncodings/big5.txt";

        $srt = TextFileFormat::getMatchingFormat($originalFilePath);

        $this->assertTrue($srt instanceof Srt);

        $storedFile = StoredFile::createFromTextFile($srt);

        $content = file_get_contents($storedFile->filePath);

        // check that file starts with Utf8-BOM
        $this->assertTrue(strpos($content, "\xEF\xBB\xBF") === 0);
    }
}
