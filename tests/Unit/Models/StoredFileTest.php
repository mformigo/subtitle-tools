<?php

namespace Tests\Unit\Models;

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
        $originalFilePath = $this->testFilesStoragePath.'text/ass/three-cues.ass';

        $storedFile = StoredFile::getOrCreate($originalFilePath);

        $this->assertSame(1, StoredFile::count());

        $this->assertFileExists($storedFile->filePath);

        $this->assertNotSame($originalFilePath, $storedFile->filePath);

        $this->assertNotEmpty($storedFile->hash);
    }

    /** @test */
    function it_copies_the_input_file()
    {
        $originalFilePath = $this->testFilesStoragePath.'text/ass/three-cues.ass';

        $storedFile = StoredFile::getOrCreate($originalFilePath);

        $this->assertFileExists($originalFilePath);
    }

    /** @test */
    function it_reuses_identical_files()
    {
        $originalFilePath = $this->testFilesStoragePath.'text/ass/three-cues.ass';

        $first = StoredFile::getOrCreate($originalFilePath);

        $second = StoredFile::getOrCreate($originalFilePath);

        $this->assertSame(1, StoredFile::count());
    }

    /** @test */
    function it_stores_text_files_with_utf8_bom()
    {
        $srt = TextFileFormat::getMatchingFormat($this->testFilesStoragePath.'text/srt/three-cues.srt');

        $this->assertTrue($srt instanceof Srt);

        $storedFile = StoredFile::createFromTextFile($srt);

        $content = file_get_contents($storedFile->filePath);

        // check that file starts with Utf8-BOM
        $this->assertTrue(strpos($content, "\xEF\xBB\xBF") === 0);
    }
}
