<?php

namespace Tests\Feature;

use App\Models\StoredFile;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CountUploadedFilesTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles;

    /** @test */
    function it_redirects_if_there_are_too_many_uploaded_files_inside_archives()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}Archives/more-than-100-files.zip")],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.too_many_files_including_archives')]);

        $this->assertSame(0, StoredFile::count());
    }
}
