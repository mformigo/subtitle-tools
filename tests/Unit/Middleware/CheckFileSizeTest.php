<?php

namespace Tests\Unit\Middleware;

use App\Models\StoredFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckFileSizeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_rejects_files_that_are_too_big()
    {
        $this->post(route('convertToSrt'), [
                'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}text/ass/too-big.ass")],
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.a_file_is_too_big')]);

        $this->assertSame(0, StoredFile::count());
    }

    /** @test */
    function it_rejects_if_files_inside_archives_are_too_big()
    {
        $this->post(route('convertToSrt'), [
                'subtitles' => [$this->createUploadedFile('archives/zip/one-file-too-big-when-extracted.zip')],
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.a_file_in_archive_too_big_when_extracted')]);

        $this->assertSame(0, StoredFile::count());
    }
}
