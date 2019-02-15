<?php

namespace Tests\Unit\Controllers;

use App\Jobs\FileJobs\ConvertToSrtJob;
use App\Models\FileGroup;
use App\Models\FileJob;
use App\Models\StoredFile;
use Illuminate\Http\UploadedFile;
use Tests\PostsFileJobs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConvertToSrtControllerTest extends TestCase
{
    use RefreshDatabase, PostsFileJobs;

    /** @test */
    function the_subtitles_field_is_server_side_required()
    {
        $response = $this->post(route('convertToSrt'));

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles' => __('validation.required', ['attribute' => 'subtitles']),
            ]);
    }

    /** @test */
    function the_subtitles_field_must_be_an_array()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => 'not an array',
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles' => __('validation.array', ['attribute' => 'subtitles']),
            ]);
    }

    /** @test */
    function the_subtitles_field_array_cant_be_empty()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles' => __('validation.required', ['attribute' => 'subtitles']),
            ]);
    }

    /** @test */
    function the_subtitles_field_array_cant_exceed_a_maximum_number_of_items()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => array_fill(0, 101, null),
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles' => __('validation.max.array', ['attribute' => 'subtitles', 'max' => 100]),
            ]);
    }

    /** @test */
    function the_subtitles_field_array_must_contain_only_valid_uploaded_files()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => array_fill(0, 10, null),
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.uploaded_files')]);
    }

    /** @test */
    function it_shows_errors_on_same_page_if_single_file_cant_be_converted()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}text/srt/empty.srt"),
            ],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('messages.cant_convert_file_to_srt')]);
    }

    /** @test */
    function it_shows_errors_on_same_page_if_single_file_has_no_dialogue()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}text/ass/cues-no-dialogue.ass"),
            ],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('messages.file_has_no_dialogue_to_convert')]);
    }

    /** @test */
    function it_can_handle_files_that_are_not_text_files()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}text/fake/dat.ass"),
            ],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('messages.not_a_text_file')]);
    }

    /** @test */
    function it_redirects_to_results_page_if_single_uploads_is_valid()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}text/ass/three-cues.ass"),
            ],
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->result_route);
    }

    /** @test */
    function it_redirects_to_results_page_if_multiple_uploads_are_valid()
    {
        $this->expectsJobs(ConvertToSrtJob::class);

        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [
                UploadedFile::fake()->create('test'),
                UploadedFile::fake()->create('test-two'),
            ],
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->result_route);
    }

    /** @test */
    function it_updates_the_file_group_when_all_jobs_finish()
    {
        $this->postConvertToSrt([
            $this->createUploadedFile('text/ass/three-cues.ass'),
            $this->createUploadedFile('text/ass/three-cues.ass'),
        ]);

        $this->assertNotNull(
            FileGroup::findOrFail(1)->file_jobs_finished_at
        );
    }

    /** @test */
    function it_can_process_the_same_file_multiple_times_in_one_request()
    {
        // Make sure the following error doesn't occur:
        //   SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'f86...f91' for key 'stored_files_hash_unique'

        $this->postConvertToSrt([
                // upload the same file 3 times...
                $this->createUploadedFile('text/srt/three-cues.srt'),
                $this->createUploadedFile('archives/zip/same-file-twice.zip'),
            ])
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $this->assertSame(2, StoredFile::count());

        FileJob::all()
            ->tap(function ($collection) {
                $this->assertCount(3, $collection);
            })
            ->each(function (FileJob $fileJob) {
                $this->assertSame(1, $fileJob->input_stored_file_id);
                $this->assertSame(2, $fileJob->output_stored_file_id);
            });
    }

    /** @test */
    function it_can_convert_srt_files_to_srt()
    {
        $this->convertAndSnapshot('text/srt/three-cues.srt');
    }

    /** @test */
    function it_can_convert_ass_files_to_srt()
    {
        $this->convertAndSnapshot('text/ass/normal01.ass');
    }

    /** @test */
    function it_can_convert_microdvd_sub_files_to_srt()
    {
        $this->convertAndSnapshot('text/microdvd/three-cues.sub');
    }

    /** @test */
    function it_can_convert_vtt_files_to_srt()
    {
        $this->convertAndSnapshot('text/vtt/three-cues.vtt');
    }

    /** @test */
    function it_can_convert_ssa_files_to_srt()
    {
        $this->convertAndSnapshot('text/ssa/normal01.ssa');
    }

    /** @test */
    function it_can_convert_otranscribe_files_to_srt()
    {
        $this->convertAndSnapshot('text/otranscribe/otranscribe-01.txt');
    }

    private function postConvertToSrt($files)
    {
        return $this->post(route('convertToSrt'), ['subtitles' => $files]);
    }

    private function convertAndSnapshot($filePath)
    {
        [$response, $fileGroup] = $this->postFileJob('convertToSrt', [
            $this->createUploadedFile($filePath),
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }
}
