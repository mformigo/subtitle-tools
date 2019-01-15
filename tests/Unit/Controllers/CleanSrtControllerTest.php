<?php

namespace Tests\Unit\Controllers;

use App\Subtitles\Tools\Options\SrtCleanerOptions;
use Tests\CreatesUploadedFiles;
use Tests\PostsFileJobs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CleanSrtControllerTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles, PostsFileJobs;

    protected $snapshotDirectory = 'srt-cleaner';

    private function postSrtCleanJob($options, $filePath = null)
    {
        [$response, $fileGroup] = $this->postFileJob('cleanSrt', [
            $this->createUploadedFile($filePath ?? 'text/srt/cleanable-01.srt'),
        ], $options);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);

        return [$response, $fileGroup];
    }

    /** @test */
    function it_can_handle_files_that_are_not_text_files()
    {
        [$response, $fileGroup] = $this->postFileJob('cleanSrt', [
            $this->createUploadedFile('text/fake/dat.ass'),
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
        $options = (new SrtCleanerOptions)
            ->stripAngle()
            ->stripCurly()
            ->stripSquare()
            ->stripParentheses();

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

    /** @test */
    function it_strips_speaker_labels()
    {
        $options = new SrtCleanerOptions();

        $options->stripSpeakerLabels = true;

        $this->postSrtCleanJob($options, 'text/srt/cleanable-02.srt');
    }

    /** @test */
    function it_strips_cues_with_music_notes()
    {
        $options = (new SrtCleanerOptions)->stripCuesWithMusicNote();

        $this->postSrtCleanJob($options, 'text/srt/cleanable-04.srt');
    }
}
