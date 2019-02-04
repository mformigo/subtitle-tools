<?php

namespace Tests\Unit\Jobs\FileJobs;

use App\Models\FileGroup;
use App\Support\Facades\TextFileFormat;
use App\Jobs\FileJobs\ConvertToSrtJob;
use App\Models\FileJob;
use App\Models\StoredFile;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConvertToSrtJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_converts_a_file_to_srt()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, $this->testFilesStoragePath.'text/ass/three-cues.ass')
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues.ass', $fileJob->original_name);

        $convertedStoredFile = $fileJob->outputStoredFile;

        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);

        $this->assertTrue($subtitle instanceof Srt);

        $this->assertCount(3, $subtitle->getCues());
    }

    /** @test */
    function it_creates_file_job_models()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, $this->testFilesStoragePath.'text/ass/three-cues.ass')
        );

        $textFileJob = FileJob::findOrFail(1);

        $this->assertSame('three-cues.ass', $textFileJob->original_name);
        $this->assertSame('srt', $textFileJob->new_extension);
        $this->assertNull($textFileJob->error_message);
        $this->assertNotNull($textFileJob->finished_at);
    }

    /** @test */
    function it_reuses_output_files_for_identical_jobs()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, $this->testFilesStoragePath.'text/ass/three-cues.ass')
        );

        dispatch(
            new ConvertToSrtJob($fileGroup, $this->testFilesStoragePath.'text/ass/three-cues.ass')
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
            new ConvertToSrtJob($fileGroup, $this->testFilesStoragePath.'text/srt/empty.srt')
        );

        $this->assertSame(1, StoredFile::count());

        $textFileJob = FileJob::findOrFail(1);

        $this->assertNotNull($textFileJob->error_message);
        $this->assertNotNull($textFileJob->finished_at);
        $this->assertNull($textFileJob->output_stored_file_id);
    }

    /** @test */
    function it_handles_files_that_have_no_valid_dialogue()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, $this->testFilesStoragePath.'text/ass/cues-no-dialogue.ass')
        );

        $this->assertSame(1, StoredFile::count());

        $textFileJob = FileJob::findOrFail(1);

        $this->assertNotNull($textFileJob->error_message);
        $this->assertNotNull($textFileJob->finished_at);
        $this->assertNull($textFileJob->output_stored_file_id);
    }

    /** @test */
    function it_allows_srt_files_as_input()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, $this->testFilesStoragePath.'text/srt/three-cues.srt')
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues.srt', $fileJob->original_name);

        $convertedStoredFile = $fileJob->outputStoredFile;

        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);

        $this->assertTrue($subtitle instanceof Srt);

        $this->assertCount(3, $subtitle->getCues());
    }

    /**
     * @param string $toolRoute
     * @param null $urlKey
     *
     * @return FileGroup
     *
     * @deprecated This is old, should be replaced by "createFileGroup" method from the "CreatesModels" trait
     */
    public function createFileGroup($toolRoute = 'default-route', $urlKey = null): FileGroup
    {
        $fileGroup = new FileGroup();

        $fileGroup->fill([
            'tool_route' => $toolRoute,
            'url_key' => $urlKey ?? generate_url_key(),
        ])->save();

        return $fileGroup;
    }
}
