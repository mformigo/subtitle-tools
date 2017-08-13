<?php

namespace Tests\Unit;

use App\Facades\TextFileFormat;
use App\Jobs\ConvertToSrtJob;
use App\Models\TextFileJob;
use App\StoredFile;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConvertToSrtJobTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function it_converts_a_stored_file_to_srt()
    {
        $storedFile = StoredFile::getOrCreate("{$this->testFilesStoragePath}TextFiles/three-cues.ass");

        dispatch(new ConvertToSrtJob($storedFile, 'three-cues.ass'));

        $convertedStoredFile = StoredFile::findOrFail(2);

        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);

        $this->assertTrue($subtitle instanceof Srt);

        $this->assertSame(3, count($subtitle->getCues()));
    }

    /** @test */
    function it_creates_text_file_job_models()
    {
        $storedFile = StoredFile::getOrCreate("{$this->testFilesStoragePath}TextFiles/three-cues.ass");

        dispatch(new ConvertToSrtJob($storedFile, 'three-cues.ass'));

        $textFileJob = TextFileJob::findOrFail(1);

        $this->assertSame('three-cues.ass', $textFileJob->original_file_name);
        $this->assertSame('srt', $textFileJob->new_extension);
        $this->assertSame(null, $textFileJob->error_message);
        $this->assertSame('convert-to-srt-index', $textFileJob->tool_route);
        $this->assertFalse(empty($textFileJob->url_key));
        $this->assertNotSame(null, $textFileJob->finished_at);
    }

    /** @test */
    function it_reuses_output_files_for_identical_jobs()
    {
        $storedFile = StoredFile::getOrCreate("{$this->testFilesStoragePath}TextFiles/three-cues.ass");

        dispatch(new ConvertToSrtJob($storedFile, 'three-cues.ass'));

        dispatch(new ConvertToSrtJob($storedFile, 'different-name.ass'));

        $firstJob = TextFileJob::findOrFail(1);

        $secondJob = TextFileJob::findOrFail(2);

        $this->assertSame($firstJob->output_stored_file_id, $secondJob->output_stored_file_id);
        $this->assertNotSame($firstJob->original_file_name, $secondJob->original_file_name);
        $this->assertNotSame($firstJob->url_key, $secondJob->url_key);
    }

    /** @test */
    function it_handles_files_that_cant_be_converted()
    {
        $storedFile = StoredFile::getOrCreate("{$this->testFilesStoragePath}TextFiles/empty.srt");

        dispatch(new ConvertToSrtJob($storedFile, 'empty'));

        $this->assertTrue(StoredFile::count() === 1);

        $textFileJob = TextFileJob::findOrFail(1);

        $this->assertNotSame(null, $textFileJob->error_message);
        $this->assertNotSame(null, $textFileJob->finished_at);
        $this->assertSame(null, $textFileJob->output_stored_file_id);
    }

    /** @test */
    function it_handles_files_that_have_no_valid_dialogue()
    {
        $storedFile = StoredFile::getOrCreate("{$this->testFilesStoragePath}TextFiles/cues-no-dialogue.ass");

        dispatch(new ConvertToSrtJob($storedFile, 'cues-no-dialogue'));

        $this->assertTrue(StoredFile::count() === 1);

        $textFileJob = TextFileJob::findOrFail(1);

        $this->assertNotSame(null, $textFileJob->error_message);
        $this->assertNotSame(null, $textFileJob->finished_at);
        $this->assertSame(null, $textFileJob->output_stored_file_id);
    }
}