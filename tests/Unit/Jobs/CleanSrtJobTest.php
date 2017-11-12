<?php

namespace Tests\Unit;

use App\Facades\TextFileFormat;
use App\Jobs\FileJobs\CleanSrtJob;
use App\Models\FileJob;
use App\Models\StoredFile;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesFileGroups;
use Tests\TestCase;

class CleanSrtJobTest extends TestCase
{
    use RefreshDatabase, CreatesFileGroups;

    /** @test */
    function it_only_accepts_srt_files()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new CleanSrtJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues.ass")
        );

        $this->assertTrue(StoredFile::count() === 1);

        $textFileJob = FileJob::findOrFail(1);

        $this->assertSame('messages.file_is_not_srt', $textFileJob->error_message);
        $this->assertNotSame(null, $textFileJob->finished_at);
        $this->assertSame(null, $textFileJob->output_stored_file_id);
    }

    /** @test */
    function it_cleans_everything_by_default()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new CleanSrtJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues-cleanable.srt")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues-cleanable.srt', $fileJob->original_name);

        $convertedStoredFile = $fileJob->outputStoredFile;

        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);

        $this->assertTrue($subtitle instanceof Srt);

        // it removes the duplicate cue
        // an extra cue is added as a watermark
        $this->assertSame(4, count($subtitle->getCues()));

        $this->assertNotContains('<i>', $subtitle->getContent());
        $this->assertNotContains('</i>', $subtitle->getContent());
        $this->assertNotContains('{', $subtitle->getContent());
        $this->assertNotContains('}', $subtitle->getContent());
    }

    /** @test */
    function job_options_can_disable_options()
    {
        $fileGroup = $this->createFileGroup();

        $fileGroup->job_options = [
          'stripCurly' => false,
          'stripAngle' => false,
        ];

        $fileGroup->save();

        dispatch(
            new CleanSrtJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues-cleanable.srt")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues-cleanable.srt', $fileJob->original_name);

        $convertedStoredFile = $fileJob->outputStoredFile;

        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);

        $this->assertTrue($subtitle instanceof Srt);

        // it still removes the duplicate cue
        // an extra cue is added as a watermark
        $this->assertSame(4, count($subtitle->getCues()));

        $this->assertContains('<i>', $subtitle->getContent());
        $this->assertContains('</i>', $subtitle->getContent());
        $this->assertContains('{', $subtitle->getContent());
        $this->assertContains('}', $subtitle->getContent());
    }
}