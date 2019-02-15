<?php

namespace Tests\Unit\Jobs\FileJobs;

use App\Models\FileGroup;
use App\Support\Facades\TextFileFormat;
use App\Jobs\FileJobs\CleanSrtJob;
use App\Models\FileJob;
use App\Models\StoredFile;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CleanSrtJobTest extends TestCase
{
    use RefreshDatabase;

//    /** @test */
//    function it_only_accepts_srt_files()
//    {
//        $fileGroup = $this->createFileGroup();
//
//        dispatch(
//            new CleanSrtJob($fileGroup, $this->testFilesStoragePath.'text/ass/three-cues.ass')
//        );
//
//        $this->assertSame(1, StoredFile::count());
//
//        $textFileJob = FileJob::findOrFail(1);
//
//        $this->assertSame('messages.file_is_not_srt', $textFileJob->error_message);
//        $this->assertNotNull($textFileJob->finished_at);
//        $this->assertNull($textFileJob->output_stored_file_id);
//    }
//
//    /** @test */
//    function job_options_can_disable_options()
//    {
//        $fileGroup = $this->createFileGroup();
//
//        $fileGroup->job_options = [
//          'stripCurly' => false,
//          'stripAngle' => false,
//        ];
//
//        $fileGroup->save();
//
//        dispatch(
//            new CleanSrtJob($fileGroup, $this->testFilesStoragePath.'text/srt/three-cues-cleanable.srt')
//        );
//
//        $fileJob = $fileGroup->fileJobs()->firstOrFail();
//
//        $this->assertSame('three-cues-cleanable.srt', $fileJob->original_name);
//
//        $convertedStoredFile = $fileJob->outputStoredFile;
//
//        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);
//
//        $this->assertInstanceOf(Srt::class, $subtitle);
//
//        // it still removes the duplicate cue
//        $this->assertCount(3, $subtitle->getCues());
//
//        $content = $subtitle->getContent();
//
//        $this->assertContains('<i>', $content);
//        $this->assertContains('</i>', $content);
//        $this->assertContains('{', $content);
//        $this->assertContains('}', $content);
//    }
//
//    /** @test */
//    function it_cleans_hearing_impaired_subtitles()
//    {
//        $fileGroup = $this->createFileGroup();
//
//        $fileGroup->update([
//            'job_options' => [
//                'stripCurly'       => false,
//                'stripAngle'       => false,
//                'stripParentheses' => true,
//            ],
//        ]);
//
//        CleanSrtJob::dispatch($fileGroup, $this->testFilesStoragePath.'text/srt/cleanable-03-hearing-impaired.srt');
//
//        $this->assertMatchesFileSnapshot(
//            StoredFile::findOrFail(2)
//        );
//    }
//
//    /**
//     * @param string $toolRoute
//     * @param null $urlKey
//     *
//     * @return FileGroup
//     *
//     * @deprecated This is old, should be replaced by "createFileGroup" method from the "CreatesModels" trait
//     */
//    public function createFileGroup($toolRoute = 'default-route', $urlKey = null): FileGroup
//    {
//        $fileGroup = new FileGroup();
//
//        $fileGroup->fill([
//            'tool_route' => $toolRoute,
//            'url_key' => $urlKey ?? generate_url_key(),
//        ])->save();
//
//        return $fileGroup;
//    }
}