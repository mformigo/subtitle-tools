<?php

namespace Tests\Unit;

use App\Jobs\Diagnostic\CollectStoredFileMetaJob;
use App\Models\StoredFile;
use App\Models\StoredFileMeta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectStoredFileMetaJobTest extends TestCase
{
    use RefreshDatabase;

    private function runCollectMetaJob($fileName = "TextFiles/three-cues.srt")
    {
        $storedFile = StoredFile::getOrCreate("{$this->testFilesStoragePath}{$fileName}");

        dispatch_now(new CollectStoredFileMetaJob($storedFile));

        return StoredFileMeta::findOrFail(1);
    }

    /** @test */
    function it_collects_the_file_size()
    {
        $meta = $this->runCollectMetaJob();

        $this->assertSame(229, $meta->size);
    }

    /** @test */
    function it_collects_the_file_mime()
    {
        $meta = $this->runCollectMetaJob();

        $this->assertSame('text/plain', $meta->mime);
    }

    /** @test */
    function it_collects_if_the_file_is_a_text_file()
    {
        $meta = $this->runCollectMetaJob();

        $this->assertSame(true, $meta->is_text_file);
    }

    /** @test */
    function it_collects_if_the_file_is_not_a_text_file()
    {
        $meta = $this->runCollectMetaJob("TextFiles/Fake/dat.ass");

        $this->assertSame(false, $meta->is_text_file);
    }

    /** @test */
    function it_collects_the_text_encoding()
    {
        $meta = $this->runCollectMetaJob();

        $this->assertSame('UTF-8', $meta->encoding);
    }

    /** @test */
    function it_collects_how_the_file_is_identified()
    {
        $meta = $this->runCollectMetaJob();

        $this->assertSame('App\Subtitles\PlainText\Srt', $meta->identified_as);
    }

    /** @test */
    function it_collects_the_line_endings()
    {
        $meta = $this->runCollectMetaJob();

        $this->assertSame("LF", $meta->line_endings);
    }

    /** @test */
    function it_collects_the_line_count()
    {
        $meta = $this->runCollectMetaJob();

        $this->assertSame(15, $meta->line_count);
    }
}
