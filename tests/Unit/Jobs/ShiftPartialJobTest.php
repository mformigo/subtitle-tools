<?php

namespace Tests\Unit;

use App\Facades\TextFileFormat;
use App\Jobs\ShiftJob;
use App\Jobs\ShiftPartialJob;
use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\CreatesFileGroups;
use Tests\TestCase;

class ShiftPartialJobTest extends TestCase
{
    use DatabaseMigrations, CreatesFileGroups;

    /** @test */
    function it_partial_shifts_a_subtitle_file()
    {
        $fileGroup = $this->createFileGroup();

        $fileGroup->job_options = [
            'shifts' => [
                ['from' => '00:00:00', 'to' => '00:00:03', 'milliseconds' => -1000],
                ['from' => '00:00:03', 'to' => '00:09:00', 'milliseconds' => 1000],
            ]
        ];

        $fileGroup->save();

        dispatch(
            new ShiftPartialJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues.srt")
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

        $this->assertSame(266, $cues[1]->getStartMs());
        $this->assertSame(2366, $cues[1]->getEndMs());

        $this->assertSame(4400, $cues[2]->getStartMs());
        $this->assertSame(7366, $cues[2]->getEndMs());

        $this->assertSame(7400, $cues[3]->getStartMs());
        $this->assertSame(9233, $cues[3]->getEndMs());
    }

    /** @test */
    function it_rejects_text_files_that_are_not_partially_shiftable()
    {
        $fileGroup = $this->createFileGroup();

        $fileGroup->job_options = [
            'shifts' => [
                ['from' => '00:00:00', 'to' => '00:00:03', 'milliseconds' => -1000],
            ]
        ];

        $fileGroup->save();

        dispatch(
            new ShiftPartialJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/Normal/normal01.smi")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('messages.file_can_not_be_partial_shifted', $fileJob->error_message);

        $this->assertNull($fileJob->output_stored_file_id);
    }

    /** @test */
    function it_rejects_files_that_are_not_text_files()
    {
        $fileGroup = $this->createFileGroup();

        $fileGroup->job_options = [
            'shifts' => [
                ['from' => '00:00:00', 'to' => '00:00:03', 'milliseconds' => -1000],
            ]
        ];

        $fileGroup->save();

        dispatch(
            new ShiftPartialJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/Fake/dat.ass")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('messages.not_a_text_file', $fileJob->error_message);

        $this->assertNull($fileJob->output_stored_file_id);
    }
}