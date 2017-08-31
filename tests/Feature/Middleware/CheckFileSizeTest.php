<?php

namespace Tests\Feature;

use App\Models\StoredFile;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckFileSizeTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles;

    /** @test */
    function it_rejects_files_that_are_too_big()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/too-big.ass")],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.a_file_is_too_big')]);

        $this->assertSame(0, StoredFile::count());
    }

    /** @test */
    function it_rejects_if_files_inside_archives_are_too_big()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}Archives/one-file-too-big-when-extracted.zip")],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.a_file_in_archive_too_big_when_extracted')]);

        $this->assertSame(0, StoredFile::count());
    }
}
