<?php

namespace Tests\Feature;

use Tests\CreatesUploadedFiles;
use Tests\PostsFileJobs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MergeControllerTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles, PostsFileJobs;

    /** @test */
    function it_can_merge_subtitles()
    {
        [$response, $fileGroup] = $this->postFileJob('merge', [], [
            'subtitles'       => $this->createUploadedFile('TextFiles/three-cues.srt'),
            'second-subtitle' => $this->createUploadedFile('TextFiles/three-cues.ass'),
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(3);
    }
}
