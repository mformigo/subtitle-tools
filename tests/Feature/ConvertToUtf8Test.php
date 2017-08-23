<?php

namespace Tests\Feature;

use App\Models\FileGroup;
use Illuminate\Http\UploadedFile;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ConvertToUtf8Test extends TestCase
{
    use DatabaseMigrations, CreatesUploadedFiles;

    /** @test */
    function the_subtitles_field_is_server_side_required()
    {
        $response = $this->post(route('convertToUtf8'));

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles' => __('validation.required', ['attribute' => 'subtitles']),
            ]);
    }

    /** @test */
    function the_subtitles_field_must_be_an_array()
    {
        $response = $this->post(route('convertToUtf8'), [
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
        $response = $this->post(route('convertToUtf8'), [
            'subtitles' => [],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles' => __('validation.required', ['attribute' => 'subtitles']),
            ]);
    }

    /** @test */
    function it_shows_errors_on_same_page_if_single_file_cant_be_converted()
    {
        $response = $this->post(route('convertToUtf8'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/Fake/exe.srt"),
            ],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('messages.not_a_text_file')]);
    }

    /** @test */
    function it_redirects_to_results_page_if_single_uploads_is_valid()
    {
        $response = $this->post(route('convertToUtf8'), [
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
        $this->expectsJobs(\App\Jobs\ConvertToUtf8Job::class);

        $response = $this->post(route('convertToUtf8'), [
            'subtitles' => [
                UploadedFile::fake()->create('test'),
                UploadedFile::fake()->create('test-two'),
            ],
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }

    /** @test */
    function it_updates_the_file_group_when_all_jobs_finish()
    {
        $response = $this->post(route('convertToUtf8'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/three-cues.ass"),
                $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/three-cues.ass"),
            ],
        ]);

        $this->assertNotNull(FileGroup::findOrFail(1)->file_jobs_finished_at);
    }
}
