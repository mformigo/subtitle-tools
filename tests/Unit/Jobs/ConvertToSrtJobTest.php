<?php

namespace Tests\Unit;

use App\Support\Facades\TextFileFormat;
use App\Jobs\FileJobs\ConvertToSrtJob;
use App\Models\FileJob;
use App\Models\StoredFile;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesFileGroups;
use Tests\TestCase;

class ConvertToSrtJobTest extends TestCase
{
    use RefreshDatabase, CreatesFileGroups;

    /** @test */
    function it_converts_a_file_to_srt()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues.ass")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues.ass', $fileJob->original_name);

        $convertedStoredFile = $fileJob->outputStoredFile;

        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);

        $this->assertTrue($subtitle instanceof Srt);

        // an extra cue is added as a watermark
        $this->assertSame(4, count($subtitle->getCues()));
    }

    /** @test */
    function it_creates_file_job_models()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues.ass")
        );

        $textFileJob = FileJob::findOrFail(1);

        $this->assertSame('three-cues.ass', $textFileJob->original_name);
        $this->assertSame('srt', $textFileJob->new_extension);
        $this->assertSame(null, $textFileJob->error_message);
        $this->assertNotSame(null, $textFileJob->finished_at);
    }

    /** @test */
    function it_reuses_output_files_for_identical_jobs()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues.ass")
        );

        dispatch(
            new ConvertToSrtJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues.ass")
        );

        $firstJob = FileJob::findOrFail(1);

        $secondJob = FileJob::findOrFail(2);

        $this->assertSame($firstJob->output_stored_file_id, $secondJob->output_stored_file_id);
    }

    /** @test */
    function it_handles_files_that_cant_be_converted()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/empty.srt")
        );

        $this->assertTrue(StoredFile::count() === 1);

        $textFileJob = FileJob::findOrFail(1);

        $this->assertNotSame(null, $textFileJob->error_message);
        $this->assertNotSame(null, $textFileJob->finished_at);
        $this->assertSame(null, $textFileJob->output_stored_file_id);
    }

    /** @test */
    function it_handles_files_that_have_no_valid_dialogue()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/cues-no-dialogue.ass")
        );

        $this->assertTrue(StoredFile::count() === 1);

        $textFileJob = FileJob::findOrFail(1);

        $this->assertNotSame(null, $textFileJob->error_message);
        $this->assertNotSame(null, $textFileJob->finished_at);
        $this->assertSame(null, $textFileJob->output_stored_file_id);
    }

    /** @test */
    function it_allows_srt_files_as_input()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues.srt")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues.srt', $fileJob->original_name);

        $convertedStoredFile = $fileJob->outputStoredFile;

        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);

        $this->assertTrue($subtitle instanceof Srt);

        // an extra cue is added as a watermark
        $this->assertSame(4, count($subtitle->getCues()));
    }
}