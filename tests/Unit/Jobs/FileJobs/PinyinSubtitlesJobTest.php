<?php

namespace Tests\Unit\Jobs\FileJobs;

use App\Models\FileGroup;
use App\Support\Facades\TextFileFormat;
use App\Jobs\FileJobs\PinyinSubtitlesJob;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PinyinSubtitlesJobTest extends TestCase
{
    use RefreshDatabase;

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