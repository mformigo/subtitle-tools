<?php

namespace Tests\Unit;

use App\Facades\TextFileFormat;
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
            new PinyinSubtitlesJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues-chinese.srt")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues-chinese.srt', $fileJob->original_name);

        $convertedStoredFile = $fileJob->outputStoredFile;

        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);

        $this->assertTrue($subtitle instanceof Srt);

        // it still removes the duplicate cue
        // an extra cue is added as a watermark
        $this->assertSame(4, count($subtitle->getCues()));

        // mode 1 should replaced all chinese with pinyin
        $this->assertNotContains('我', $subtitle->getContent());
    }

}