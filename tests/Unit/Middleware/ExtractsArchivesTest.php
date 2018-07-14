<?php

namespace Tests\Unit\Middleware;

use App\Models\FileJob;
use App\Models\StoredFile;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExtractsArchivesTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles;

    /** @test */
    function it_extracts_zips_in_request()
    {
        $this->withoutJobs();

        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}Archives/5-text-files-4-good.zip")],
        ]);

        $response->assertStatus(302);

        $this->assertSame(5, StoredFile::count());
    }

    /** @test */
    function it_extracts_rars_in_request()
    {
        $this->withoutJobs();

        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}Archives/Rar/zimuku-10-ass.rar")],
        ]);

        $response->assertStatus(302);

        $this->assertSame(10, StoredFile::count());
    }

    /** @test */
    function it_extracts_multiple_zips_in_request()
    {
        $this->withoutJobs();

        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}Archives/5-text-files-4-good.zip"),
                $this->createUploadedFile("{$this->testFilesStoragePath}Archives/dirs-with-ass.zip"),
            ],
        ]);

        $response->assertStatus(302);

        $this->assertSame(13, StoredFile::count());
    }

    /** @test */
    function it_rejects_empty_zips()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}Archives/empty.zip")],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.no_files_after_extracting_archives')]);

        $this->assertSame(0, StoredFile::count());
    }

    /** @test */
    function it_rejects_zips_with_only_directories_inside()
    {
        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}Archives/one-empty-dir.zip")],
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.no_files_after_extracting_archives')]);

        $this->assertSame(0, StoredFile::count());
    }

    /** @test */
    function it_removes_archives_from_the_request()
    {
        $this->withoutJobs();

        $response = $this->post(route('convertToSrt'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}Archives/one-srt.zip")],
        ]);

        $response->assertStatus(302);

        $this->assertSame(1, StoredFile::count());
        $this->assertTrue(ends_with(FileJob::findOrFail(1)->original_name, '.srt'));
    }
}
