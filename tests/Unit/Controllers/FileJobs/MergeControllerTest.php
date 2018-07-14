<?php

namespace Tests\Unit\Controllers\FileJobs;

use Tests\CreatesUploadedFiles;
use Tests\PostsFileJobs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MergeControllerTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles, PostsFileJobs;

    protected $fileSnapshotDirectory = 'merge';

    private function postMergeJob($attributes)
    {
        return $this->postFileJob('merge', [], $attributes + [
            'nearest_cue_threshold' => 1000,
            'mode'                  => 'simple',
        ]);
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_srt_files()
    {
        [$response, $fileGroup] = $this->postMergeJob([
            'subtitles'       => $this->createUploadedFile('TextFiles/three-cues.srt'),
            'second-subtitle' => $this->createUploadedFile('TextFiles/three-cues.ass'),
            'mode'            => 'simple',
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(3);
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_ass_files()
    {
        [$response, $fileGroup] = $this->postMergeJob([
            'subtitles'       => $this->createUploadedFile('TextFiles/three-cues.ass'),
            'second-subtitle' => $this->createUploadedFile('TextFiles/three-cues.srt'),
            'mode'            => 'simple',
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(3);
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_ssa_files()
    {
        [$response, $fileGroup] = $this->postMergeJob([
            'subtitles'       => $this->createUploadedFile('TextFiles/three-cues.ssa'),
            'second-subtitle' => $this->createUploadedFile('TextFiles/three-cues.srt'),
            'mode'            => 'simple',
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(3);
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_vtt_files()
    {
        [$response, $fileGroup] = $this->postMergeJob([
            'subtitles'       => $this->createUploadedFile('TextFiles/three-cues.vtt'),
            'second-subtitle' => $this->createUploadedFile('TextFiles/three-cues.srt'),
            'mode'            => 'simple',
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(3);
    }

    /** @test */
    function it_can_nearest_cue_merge_subtitles_into_ass_files()
    {
        [$response, $fileGroup] = $this->postMergeJob([
            'subtitles'             => $this->createUploadedFile('TextFiles/merge-tool/merge.ass'),
            'second-subtitle'       => $this->createUploadedFile('TextFiles/merge-tool/merge.srt'),
            'nearest_cue_threshold' => 3000,
            'mode'                  => 'nearestCueThreshold',
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(3);
    }

    /** @test */
    function it_can_top_bottom_merge_subtitles_into_ass_files()
    {
        [$response, $fileGroup] = $this->postMergeJob([
            'subtitles'       => $this->createUploadedFile('TextFiles/three-cues.ass'),
            'second-subtitle' => $this->createUploadedFile('TextFiles/three-cues.srt'),
            'mode'            => 'topBottom',
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(3);
    }

    /** @test */
    function it_can_top_bottom_merge_subtitles_into_srt_files()
    {
        [$response, $fileGroup] = $this->postMergeJob([
            'subtitles'       => $this->createUploadedFile('TextFiles/three-cues.srt'),
            'second-subtitle' => $this->createUploadedFile('TextFiles/three-cues.ass'),
            'mode'            => 'topBottom',
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(3);
    }
}
