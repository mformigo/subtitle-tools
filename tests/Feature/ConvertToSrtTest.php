<?php

namespace Tests\Feature;

use App\Models\FileGroup;
use Illuminate\Http\UploadedFile;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ConvertToSrtTest extends TestCase
{
    use DatabaseMigrations, CreatesUploadedFiles;

    /** @test */
    function the_subtitles_field_is_server_side_required()
    {
        $response = $this->post(route('convert-to-srt'));

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles' => __('validation.required', ['attribute' => 'subtitles']),
            ]);
    }

    /** @test */
    function the_subtitles_field_must_be_an_array()
    {
        $response = $this->post(route('convert-to-srt'), [
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
        $response = $this->post(route('convert-to-srt'), [
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
        $response = $this->post(route('convert-to-srt'), [
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
        $response = $this->post(route('convert-to-srt'), [
            'subtitles' => array_fill(0, 10, null),
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.uploaded_files')]);
    }

    /** @test */
    function it_shows_errors_on_same_page_if_single_file_cant_be_converted()
    {
        $response = $this->post(route('convert-to-srt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/empty.srt"),
            ],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(["subtitles" => __('messages.cant_convert_file_to_srt')]);
    }

    /** @test */
    function it_shows_errors_on_same_page_if_single_file_has_no_dialogue()
    {
        $response = $this->post(route('convert-to-srt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/cues-no-dialogue.ass"),
            ],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(["subtitles" => __('messages.file_has_no_dialogue_to_convert')]);
    }

    /** @test */
    function it_redirects_to_results_page_if_single_uploads_is_valid()
    {
        $response = $this->post(route('convert-to-srt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/three-cues.ass"),
            ],
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }

    /** @test */
    function it_redirects_to_results_page_if_multiple_uploads_are_valid()
    {
        $this->expectsJobs(\App\Jobs\ConvertToSrtJob::class);

        $response = $this->post(route('convert-to-srt'), [
            'subtitles' => [
                UploadedFile::fake()->create('test'),
                UploadedFile::fake()->create('test-two'),
            ],
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }
}
