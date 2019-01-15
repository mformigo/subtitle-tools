<?php

namespace Tests\Unit\Controllers;

use Tests\CreatesUploadedFiles;
use Tests\PostsFileJobs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MergeControllerTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles, PostsFileJobs;

    protected $snapshotDirectory = 'merge';

    /** @test */
    function it_can_simple_merge_subtitles_into_srt_files()
    {
        $this->snapshotSimpleMerge('text/srt/three-cues.srt', 'text/ass/three-cues.ass');
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_ass_files()
    {
        $this->snapshotSimpleMerge('text/ass/three-cues.ass', 'text/srt/three-cues.srt');
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_ssa_files()
    {
        $this->snapshotSimpleMerge('text/ssa/three-cues.ssa', 'text/srt/three-cues.srt');
    }

    /** @test */
    function it_can_simple_merge_subtitles_into_vtt_files()
    {
        $this->snapshotSimpleMerge('text/vtt/three-cues.vtt', 'text/srt/three-cues.srt');
    }

    /** @test */
    function it_can_nearest_cue_merge_subtitles_into_ass_files()
    {
        $this->snapshotNearestCueMerge('text/ass/merge-01.ass', 'text/srt/merge-01.srt', 3000);
    }

    /** @test */
    function it_can_nearest_cue_merge_two_srt_files()
    {
        $this->snapshotNearestCueMerge('text/srt/three-cues.srt', 'text/srt/three-cues-chinese.srt', 3000);
    }

    /** @test */
    function it_can_top_bottom_merge_subtitles_into_ass_files()
    {
        $this->snapshotTopBottomMerge('text/ass/three-cues.ass', 'text/srt/three-cues.srt');
    }

    /** @test */
    function it_can_top_bottom_merge_subtitles_into_srt_files()
    {
        $this->snapshotTopBottomMerge('text/srt/three-cues.srt', 'text/ass/three-cues.ass');
    }

    /** @test */
    function it_can_glue_merge_if_the_base_subtitle_has_no_cues()
    {
        $this->snapshotGlueMerge('text/vtt/webvtt-no-dialogue.vtt', 'text/srt/three-cues.srt', 1000);
    }

    /** @test */
    function it_can_glue_two_srt_files_end_to_end()
    {
        $this->snapshotGlueMerge('text/srt/three-cues.srt', 'text/srt/three-cues-chinese.srt', 0);

        $this->snapshotGlueMerge('text/srt/three-cues.srt', 'text/srt/three-cues-chinese.srt', 3000);

        $this->snapshotGlueMerge('text/srt/three-cues.srt', 'text/srt/three-cues-chinese.srt', -3000);

        $this->snapshotGlueMerge('text/srt/three-cues.srt', 'text/srt/three-cues-chinese.srt', -99999);
    }

    /** @test */
    function it_can_glue_merge_two_different_formats()
    {
        $this->snapshotGlueMerge('text/srt/three-cues.srt', 'text/ass/three-cues.ass', 1000);

        $this->snapshotGlueMerge('text/ass/three-cues.ass', 'text/srt/three-cues.srt', 1000);

        $this->snapshotGlueMerge('text/ssa/three-cues.ssa', 'text/srt/three-cues.srt', 1000);

        $this->snapshotGlueMerge('text/vtt/three-cues.vtt', 'text/ass/three-cues.ass', 1000);
    }

    private function snapshotSimpleMerge($baseFile, $mergeFile)
    {
        $this->snapshotMerge([
            'subtitles'       => $this->createUploadedFile($baseFile),
            'second-subtitle' => $this->createUploadedFile($mergeFile),
            'mode'            => 'simple',
        ]);
    }

    private function snapshotTopBottomMerge($baseFile, $mergeFile)
    {
        $this->snapshotMerge([
            'subtitles'       => $this->createUploadedFile($baseFile),
            'second-subtitle' => $this->createUploadedFile($mergeFile),
            'mode'            => 'topBottom',
        ]);
    }

    private function snapshotNearestCueMerge($baseFile, $mergeFile, $threshold)
    {
        $this->snapshotMerge([
            'subtitles'             => $this->createUploadedFile($baseFile),
            'second-subtitle'       => $this->createUploadedFile($mergeFile),
            'nearest_cue_threshold' => $threshold,
            'mode'                  => 'nearestCueThreshold',
        ]);
    }

    private function snapshotGlueMerge($baseFile, $mergeFile, $offset)
    {
        $this->snapshotMerge([
            'subtitles'       => $this->createUploadedFile($baseFile),
            'second-subtitle' => $this->createUploadedFile($mergeFile),
            'glue_offset'     => $offset,
            'mode'            => 'glue',
        ]);
    }

    private function snapshotMerge($attributes)
    {
        [$response, $fileGroup] = $this->postFileJob('merge', [], $attributes + [
            'nearest_cue_threshold' => 1000,
            'glue_offset'           => 1000,
            'mode'                  => 'simple',
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(
            $fileGroup->fileJobs->first()->output_stored_file_id
        );
    }
}
