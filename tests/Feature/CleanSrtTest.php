<?php

namespace Tests\Feature;

use App\Models\FileGroup;
use App\Models\StoredFile;
use Illuminate\Http\UploadedFile;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CleanSrtTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles;

    /** @test */
    function the_subtitles_field_is_server_side_required()
    {
        $response = $this->post(route('cleanSrt'));

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles' => __('validation.required', ['attribute' => 'subtitles']),
            ]);
    }

    /** @test */
    function the_subtitles_field_must_be_an_array()
    {
        $response = $this->post(route('cleanSrt'), [
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
        $response = $this->post(route('cleanSrt'), [
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
        $response = $this->post(route('cleanSrt'), [
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
        $response = $this->post(route('cleanSrt'), [
            'subtitles' => array_fill(0, 10, null),
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.uploaded_files')]);
    }

    /** @test */
    function it_shows_errors_on_same_page_if_single_file_cant_be_cleaned()
    {
        $response = $this->post(route('cleanSrt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/empty.srt"),
            ],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('messages.file_is_not_srt')]);
    }

    /** @test */
    function it_can_handle_files_that_are_not_text_files()
    {
        $response = $this->post(route('cleanSrt'), [
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
        $response = $this->post(route('cleanSrt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/three-cues.srt"),
            ],
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }

    /** @test */
    function it_redirects_to_results_page_if_multiple_uploads_are_valid()
    {
        $this->expectsJobs(\App\Jobs\CleanSrtJob::class);

        $response = $this->post(route('cleanSrt'), [
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
    function it_uses_job_options()
    {
        $response = $this->post(route('cleanSrt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/three-cues-cleanable.srt"),
            ],
            // not setting the checkbox options means they are not checked
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $outputContent = app('TextFileReader')->getContents(StoredFile::findOrFail(2)->filePath);

        // assert angle brackets and curly brackets were not cleaned
        $this->assertContains('<i>', $outputContent);
        $this->assertContains('{', $outputContent);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }

    /** @test */
    function it_uses_job_options_when_present()
    {
        $response = $this->post(route('cleanSrt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/three-cues-cleanable.srt"),
            ],
            'stripCurly' => 'checked',
            'stripAngle' => 'checked',
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $outputContent = app('TextFileReader')->getContents(StoredFile::findOrFail(2)->filePath);

        $this->assertNotContains('<i>', $outputContent);
        $this->assertNotContains('{', $outputContent);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }
}
