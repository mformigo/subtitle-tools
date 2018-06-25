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
        $this->convertAndSnapshot('TextFiles/three-cues.ass');
    }

    /** @test */
    function it_can_convert_a_vtt_with_indexes_to_vtt()
    {
        $this->convertAndSnapshot('TextFiles/Normal/normal02.vtt');
    }

    /** @test */
    function it_can_convert_a_vtt_without_indexes_to_vtt()
    {
        $this->convertAndSnapshot('TextFiles/Normal/normal03.vtt');
    }

    /** @test */
    function it_can_convert_otranscribe_files_to_vtt()
    {
        $this->convertAndSnapshot('TextFiles/otranscribe/otranscribe-1.txt');
    }

    private function convertAndSnapshot($filePath)
    {
        [$response, $fileGroup] = $this->postFileJob('convertToVtt', [
            $this->createUploadedFile($filePath),
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }
}
