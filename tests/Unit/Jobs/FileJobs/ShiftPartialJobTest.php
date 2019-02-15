<?php

namespace Tests\Unit\Jobs\FileJobs;

use App\Models\FileGroup;
use App\Support\Facades\TextFileFormat;
use App\Jobs\FileJobs\ShiftPartialJob;
use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PlainText\Srt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftPartialJobTest extends TestCase
{
    use RefreshDatabase;
//
//    /** @test */
//    function it_partial_shifts_a_subtitle_file()
//    {
//        $fileGroup = $this->createFileGroup();
//
//        $fileGroup->job_options = [
//            'shifts' => [
//                ['from' => '00:00:00', 'to' => '00:00:03', 'milliseconds' => -1000],
//                ['from' => '00:00:03', 'to' => '00:09:00', 'milliseconds' => 1000],
//            ]
//        ];
//
//        $fileGroup->save();
//
//        dispatch(
//            new ShiftPartialJob($fileGroup, $this->testFilesStoragePath.'text/srt/three-cues.srt')
//        );
//
//        $fileJob = $fileGroup->fileJobs()->firstOrFail();
//
//        $this->assertSame('three-cues.srt', $fileJob->original_name);
//
//        $convertedStoredFile = $fileJob->outputStoredFile;
//
//        /** @var $subtitle ContainsGenericCues */
//        $subtitle = TextFileFormat::getMatchingFormat($convertedStoredFile->filePath);
//
//        $this->assertTrue($subtitle instanceof Srt);
//
//        $cues = $subtitle->getCues();
//
//        $this->assertSame(3, count($cues));
//
//        $this->assertSame(266, $cues[0]->getStartMs());
//        $this->assertSame(2366, $cues[0]->getEndMs());
//
//        $this->assertSame(4400, $cues[1]->getStartMs());
//        $this->assertSame(7366, $cues[1]->getEndMs());
//
//        $this->assertSame(7400, $cues[2]->getStartMs());
//        $this->assertSame(9233, $cues[2]->getEndMs());
//    }
//
//    /** @test */
//    function it_rejects_text_files_that_are_not_partially_shiftable()
//    {
//        $fileGroup = $this->createFileGroup();
//
//        $fileGroup->job_options = [
//            'shifts' => [
//                ['from' => '00:00:00', 'to' => '00:00:03', 'milliseconds' => -1000],
//            ]
//        ];
//
//        $fileGroup->save();
//
//        dispatch(
//            new ShiftPartialJob($fileGroup, $this->testFilesStoragePath.'text/smi/normal01.smi')
//        );
//
//        $fileJob = $fileGroup->fileJobs()->firstOrFail();
//
//        $this->assertSame('messages.file_can_not_be_partial_shifted', $fileJob->error_message);
//
//        $this->assertNull($fileJob->output_stored_file_id);
//    }
//
//    /** @test */
//    function it_rejects_files_that_are_not_text_files()
//    {
//        $fileGroup = $this->createFileGroup();
//
//        $fileGroup->job_options = [
//            'shifts' => [
//                ['from' => '00:00:00', 'to' => '00:00:03', 'milliseconds' => -1000],
//            ]
//        ];
//
//        $fileGroup->save();
//
//        dispatch(
//            new ShiftPartialJob($fileGroup, $this->testFilesStoragePath.'text/fake/dat.ass')
//        );
//
//        $fileJob = $fileGroup->fileJobs()->firstOrFail();
//
//        $this->assertSame('messages.not_a_text_file', $fileJob->error_message);
//
//        $this->assertNull($fileJob->output_stored_file_id);
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