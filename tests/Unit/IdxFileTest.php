<?php

namespace Tests\Unit;

use App\Subtitles\VobSub\IdxFile;
use Tests\TestCase;

class IdxFileTest extends TestCase
{
    /** @test */
    function it_reads_languages_from_idx_files()
    {
        $idxFile = new IdxFile("{$this->testFilesStoragePath}SubIdxFiles/error-and-nl.idx");

        $this->assertSame('unknown', $idxFile->getLanguageForIndex(0));

        $this->assertSame('nl', $idxFile->getLanguageForIndex(1));
    }

    /** @test */
    function it_returns_unknown_for_non_existing_indexes()
    {
        $idxFile = new IdxFile("{$this->testFilesStoragePath}SubIdxFiles/error-and-nl.idx");

        $this->assertSame('unknown', $idxFile->getLanguageForIndex(99));
    }
}
