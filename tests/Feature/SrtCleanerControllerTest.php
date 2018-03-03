<?php

namespace Tests\Feature;

use App\Subtitles\Tools\Options\SrtCleanerOptions;
use Tests\CreatesUploadedFiles;
use Tests\PostsFileJobs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SrtCleanerControllerTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles, PostsFileJobs;

    protected $fileSnapshotDirectory = 'srt-cleaner';

    private function postSrtCleanJob($options)
    {
        [$response, $fileGroup] = $this->postFileJob('cleanSrt', [
            $this->createUploadedFile('TextFiles/srt-cleaner-tool/cleanable-01.srt'),
        ], $options);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);

        return [$response, $fileGroup];
    }

    /** @test */
    function it_can_handle_files_that_are_not_text_files()
    {
        [$response, $fileGroup] = $this->postFileJob('cleanSrt', [
            $this->createUploadedFile('TextFiles/Fake/exe.srt'),
        ], [
            // no options
        ]);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('messages.not_a_text_file')]);
    }

    /** @test */
    function it_cleans_nothing_by_default()
    {
        $this->postSrtCleanJob(
            new SrtCleanerOptions()
        );
    }

    /** @test */
    function it_can_clean_four_kinds_of_brackets()
    {
        $options = new SrtCleanerOptions();

        $options->stripAngle = true;
        $options->stripCurly = true;
        $options->stripSquare= true;
        $options->stripParentheses = true;

        $this->postSrtCleanJob($options);
    }

    /** @test */
    function it_can_clean_angle_brackets()
    {
        $options = new SrtCleanerOptions();

        $options->stripAngle = true;

        $this->postSrtCleanJob($options);
    }

    /** @test */
    function it_can_clean_curly_brackets()
    {
        $options = new SrtCleanerOptions();

        $options->stripCurly = true;

        $this->postSrtCleanJob($options);
    }

    /** @test */
    function it_can_clean_parentheses()
    {
        $options = new SrtCleanerOptions();

        $options->stripParentheses = true;

        $this->postSrtCleanJob($options);
    }

    /** @test */
    function it_can_clean_square_brackets()
    {
        $options = new SrtCleanerOptions();

        $options->stripSquare = true;

        $this->postSrtCleanJob($options);
    }
}
