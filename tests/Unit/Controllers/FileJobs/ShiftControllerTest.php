<?php

namespace Tests\Unit\Controllers\FileJobs;

use App\Models\FileGroup;
use Illuminate\Http\UploadedFile;
use Tests\CreatesUploadedFiles;
use Tests\PostsFileJobs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShiftControllerTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles, PostsFileJobs;

    /** @test */
    function the_fields_are_server_side_required()
    {
        $this->post(route('shift'))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles'    => __('validation.required', ['attribute' => 'subtitles']),
                'milliseconds' => __('validation.required', ['attribute' => 'milliseconds']),
            ]);
    }

    /** @test */
    function it_validates_the_milliseconds_field()
    {
        $this->post(route('shift'), [
            'milliseconds' => 'not a number',
        ])->assertStatus(302)->assertSessionHasErrors(['milliseconds' => __('validation.numeric', ['attribute' => 'milliseconds'])]);

        $this->post(route('shift'), [
            'milliseconds' => '',
        ])->assertStatus(302)->assertSessionHasErrors(['milliseconds' => __('validation.required', ['attribute' => 'milliseconds'])]);

        $this->post(route('shift'), [
            'milliseconds' => '1e3',
        ])->assertStatus(302)->assertSessionHasErrors(['milliseconds' => __('validation.regex', ['attribute' => 'milliseconds'])]);
    }

    /** @test */
    function milliseconds_can_not_be_zero()
    {
        $this->post(route('shift'), [
            'milliseconds' => 0,
        ])->assertStatus(302)->assertSessionHasErrors(['milliseconds' => __('validation.not_in', ['attribute' => 'milliseconds'])]);
    }

    /** @test */
    function it_shows_errors_on_same_page_if_single_file_cant_be_shifted()
    {
        $this->post(route('shift'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}text/srt/empty.srt")],
            'milliseconds' => 1000,
        ])->assertStatus(302)->assertSessionHasErrors(['subtitles' => __('messages.file_can_not_be_shifted')]);
    }

    /** @test */
    function it_shows_errors_on_same_page_if_single_file_is_not_a_text_file()
    {
        $this->post(route('shift'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}text/fake/dat.ass")],
            'milliseconds' => 1000,
        ])->assertStatus(302)->assertSessionHasErrors(['subtitles' => __('messages.not_a_text_file')]);
    }

    /** @test */
    function it_redirects_to_results_page_if_single_uploads_is_valid()
    {
        $response = $this->post(route('shift'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}text/ass/three-cues.ass")],
            'milliseconds' => 1000,
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }

    /** @test */
    function it_redirects_to_results_page_if_multiple_uploads_are_valid()
    {
        $this->expectsJobs(\App\Jobs\FileJobs\ShiftJob::class);

        $response = $this->post(route('shift'), [
            'subtitles' => [
                UploadedFile::fake()->create('test'),
                UploadedFile::fake()->create('test-two'),
            ],
            'milliseconds' => 1000,
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }

    /** @test */
    function it_updates_the_file_group_when_all_jobs_finish()
    {
        $this->post(route('shift'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}text/ass/three-cues.ass"),
                $this->createUploadedFile("{$this->testFilesStoragePath}text/ass/three-cues.ass"),
            ],
            'milliseconds' => 1000,
        ]);

        $this->assertNotNull(FileGroup::findOrFail(1)->file_jobs_finished_at);
    }

    /** @test */
    function it_can_shift_vtt_files()
    {
        [$response, $fileGroup] = $this->postFileJob('shift', [
            $this->createUploadedFile('text/vtt/three-cues.vtt'),
        ], [
            'milliseconds' => 1000,
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }

    /** @test */
    function it_can_shift_ass_files()
    {
        [$response, $fileGroup] = $this->postFileJob('shift', [
            $this->createUploadedFile('text/ass/normal01.ass'),
        ], [
            'milliseconds' => 1000,
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }

    /** @test */
    function it_can_shift_ssa_files()
    {
        [$response, $fileGroup] = $this->postFileJob('shift', [
            $this->createUploadedFile('text/ssa/normal01.ssa'),
        ], [
            'milliseconds' => -1000,
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }
}
