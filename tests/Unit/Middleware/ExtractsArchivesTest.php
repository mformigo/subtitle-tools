<?php

namespace Tests\Unit\Middleware;

use App\Models\FileJob;
use App\Models\StoredFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExtractsArchivesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_extracts_zips_in_request()
    {
        $this->withoutJobs();

        $this->post(route('convertToSrt'), [
                'subtitles' => [$this->createUploadedFile('archives/zip/5-text-files-4-good.zip')],
            ])
            ->assertStatus(302);

        $this->assertSame(5, StoredFile::count());
    }

    /** @test */
    function it_extracts_rars_in_request()
    {
        $this->withoutJobs();

        $this->post(route('convertToSrt'), [
            'subtitles' => [$this->createUploadedFile('archives/rar/zimuku-10-ass.rar')],
            ])
            ->assertStatus(302);

        $this->assertSame(10, StoredFile::count());
    }

    /** @test */
    function it_extracts_multiple_zips_in_request()
    {
        $this->withoutJobs();

        $this->post(route('convertToSrt'), [
                'subtitles' => [
                    $this->createUploadedFile('archives/zip/5-text-files-4-good.zip'),
                    $this->createUploadedFile('archives/zip/dirs-with-ass.zip'),
                ],
            ])
            ->assertStatus(302);

        $this->assertSame(13, StoredFile::count());
    }

    /** @test */
    function it_rejects_empty_zips()
    {
        $this->post(route('convertToSrt'), [
                'subtitles' => [$this->createUploadedFile('archives/zip/empty.zip')],
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.no_files_after_extracting_archives')]);

        $this->assertSame(0, StoredFile::count());
    }

    /** @test */
    function it_rejects_zips_with_only_directories_inside()
    {
        $this->post(route('convertToSrt'), [
                'subtitles' => [$this->createUploadedFile('archives/zip/one-empty-dir.zip')],
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors(['subtitles' => __('validation.no_files_after_extracting_archives')]);

        $this->assertSame(0, StoredFile::count());
    }

    /** @test */
    function it_removes_archives_from_the_request()
    {
        $this->withoutJobs();

        $this->post(route('convertToSrt'), [
                'subtitles' => [$this->createUploadedFile('archives/zip/one-srt.zip')],
            ])
            ->assertStatus(302);

        $storedFiles = StoredFile::all();

        $this->assertCount(2, $storedFiles);

        $this->assertSame('text/plain', Storage::mimeType($storedFiles->first()->storage_file_path));
        $this->assertSame('text/plain', Storage::mimeType($storedFiles->last()->storage_file_path));

        $this->assertStringEndsWith('.srt', FileJob::findOrFail(1)->original_name);
    }
}
