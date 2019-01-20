<?php

namespace Tests\Unit\Middleware;

use App\Models\StoredFile;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CountUploadedFilesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_redirects_if_there_are_too_many_uploaded_files_inside_archives()
    {
        $this->post(route('convertToSrt'), [
                'subtitles' => [$this->createUploadedFile('archives/zip/more-than-100-files.zip')],
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.too_many_files_including_archives')]);

        $this->assertSame(0, StoredFile::count());
    }
}
