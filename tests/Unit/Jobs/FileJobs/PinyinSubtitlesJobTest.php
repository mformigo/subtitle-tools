<?php

namespace Tests\Unit\Jobs\FileJobs;

use App\Support\Facades\TextFileFormat;
use App\Jobs\FileJobs\PinyinSubtitlesJob;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesFileGroups;
use Tests\TestCase;

class PinyinSubtitlesJobTest extends TestCase
{
    use RefreshDatabase, CreatesFileGroups;

    /** @test */
    function it_makes_pinyin_subtitles()
    {
        $fileGroup = $this->createFileGroup();

        $fileGroup->job_options = [
          'mode' => '1',
        ];

        $fileGroup->save();

        dispatch(
            new PinyinSubtitlesJob($fileGroup, $this->testFilesStoragePath.'text/srt/three-cues-chinese.srt')
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues-chinese.srt', $fileJob->original_name);

        $convertedStoredFile = $fileJob->outputStoredFile;

        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);

        $this->assertTrue($subtitle instanceof Srt);

        // it still removes the duplicate cue
        $this->assertCount(3, $subtitle->getCues());

        // mode 1 should replaced all chinese with pinyin
        $this->assertNotContains('我', $subtitle->getContent());
    }

}