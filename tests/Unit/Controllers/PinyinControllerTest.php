<?php

namespace Tests\Unit\Controllers;

use App\Models\FileGroup;
use Illuminate\Http\UploadedFile;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PinyinControllerTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles;

    /** @test */
    function mode_job_option_is_required()
    {
        $response = $this->post(route('pinyin'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}text/srt/three-cues-cleanable.srt")],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['mode' => __('validation.required', ['attribute' => 'mode'])]);
    }

    /** @test */
    function mode_job_option_cant_be_empty()
    {
        $response = $this->post(route('pinyin'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}text/srt/three-cues-cleanable.srt")],
            'mode' => '',
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['mode' => __('validation.required', ['attribute' => 'mode'])]);
    }

    /** @test */
    function mode_must_be_valid()
    {
        $response = $this->post(route('pinyin'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}text/srt/three-cues-cleanable.srt")],
            'mode' => '4',
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['mode' => __('validation.not_in', ['attribute' => 'mode'])]);
    }

    /** @test */
    function the_subtitles_field_is_server_side_required()
    {
        $response = $this->post(route('pinyin'));

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles' => __('validation.required', ['attribute' => 'subtitles']),
            ]);
    }

    /** @test */
    function the_subtitles_field_must_be_an_array()
    {
        $response = $this->post(route('pinyin'), [
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
        $response = $this->post(route('pinyin'), [
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
        $response = $this->post(route('pinyin'), [
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
        $response = $this->post(route('pinyin'), [
            'subtitles' => array_fill(0, 10, null),
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.uploaded_files')]);
    }

    /** @test */
    function it_always_queues_jobs()
    {
        $response = $this->post(route('pinyin'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}text/srt/empty.srt"),
            ],
            'mode' => '3',
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }

    /** @test */
    function it_redirects_to_results_page_if_multiple_uploads_are_valid()
    {
        $this->expectsJobs(\App\Jobs\FileJobs\PinyinSubtitlesJob::class);

        $response = $this->post(route('pinyin'), [
            'subtitles' => [
                UploadedFile::fake()->create('test'),
                UploadedFile::fake()->create('test-two'),
            ],
            'mode' => '2',
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }
}
