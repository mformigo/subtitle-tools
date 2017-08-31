<?php

namespace Tests\Unit;

use App\Facades\TextFileFormat;
use App\Jobs\ShiftJob;
use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesFileGroups;
use Tests\TestCase;

class ShiftJobTest extends TestCase
{
    use RefreshDatabase, CreatesFileGroups;

    /** @test */
    function it_shifts_a_subtitle_file()
    {
        $fileGroup = $this->createFileGroup();

        $fileGroup->job_options = [
            'milliseconds' => 1000,
        ];

        $fileGroup->save();

        dispatch(
            new ShiftJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues.srt")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues.srt', $fileJob->original_name);

        $convertedStoredFile = $fileJob->outputStoredFile;

        /** @var $subtitle ContainsGenericCues */
        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);

        $this->assertTrue($subtitle instanceof Srt);

        // an extra cue is added as a watermark
        $cues = $subtitle->getCues();

        $this->assertSame(4, count($cues));

        $this->assertSame(2266, $cues[1]->getStartMs());
        $this->assertSame(4366, $cues[1]->getEndMs());

        $this->assertSame(4400, $cues[2]->getStartMs());
        $this->assertSame(7366, $cues[2]->getEndMs());
    }

    /** @test */
    function it_rejects_text_files_that_are_not_shiftable()
    {
        $fileGroup = $this->createFileGroup();

        $fileGroup->job_options = [
            'milliseconds' => 1000,
        ];

        $fileGroup->save();

        dispatch(
            new ShiftJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/Normal/normal01.txt")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('messages.file_can_not_be_shifted', $fileJob->error_message);

        $this->assertNull($fileJob->output_stored_file_id);
    }

    /** @test */
    function it_rejects_files_that_are_not_text_files()
    {
        $fileGroup = $this->createFileGroup();

        $fileGroup->job_options = [
            'milliseconds' => 1000,
        ];

        $fileGroup->save();

        dispatch(
            new ShiftJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/Fake/dat.ass")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('messages.not_a_text_file', $fileJob->error_message);

        $this->assertNull($fileJob->output_stored_file_id);
    }
}