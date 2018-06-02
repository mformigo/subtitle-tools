<?php

namespace Tests\Feature;

use Tests\CreatesUploadedFiles;
use Tests\PostsFileJobs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConvertToVttTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles, PostsFileJobs;

    /** @test */
    function it_converts_files_to_vtt()
    {
        [$response, $fileGroup] = $this->postFileJob('convertToVtt', [
            $this->createUploadedFile('TextFiles/three-cues.ass'),
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }

    /** @test */
    function it_can_convert_a_vtt_with_indexes_to_vtt()
    {
        [$response, $fileGroup] = $this->postFileJob('convertToVtt', [
            $this->createUploadedFile('TextFiles/Normal/normal02.vtt'),
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }

    /** @test */
    function it_can_convert_a_vtt_without_indexes_to_vtt()
    {
        [$response, $fileGroup] = $this->postFileJob('convertToVtt', [
            $this->createUploadedFile('TextFiles/Normal/normal03.vtt'),
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }
}
