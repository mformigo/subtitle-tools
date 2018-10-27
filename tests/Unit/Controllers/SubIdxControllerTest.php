<?php

namespace Tests\Unit\Controllers;

use App\Models\StoredFile;
use App\Models\SubIdx;
use Illuminate\Http\UploadedFile;
use Tests\CreatesUploadedFiles;
use Tests\MocksVobSub2Srt;
use Tests\PostsVobSubs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubIdxControllerTest extends TestCase
{
    use RefreshDatabase, MocksVobSub2Srt, PostsVobSubs, CreatesUploadedFiles;

    /** @test */
    function the_sub_and_idx_file_are_server_side_required()
    {
        $response = $this->post(route('subIdx'));

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'sub' => __('validation.required', ['attribute' => 'sub']),
                'idx' => __('validation.required', ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_rejects_empty_files()
    {
        $response = $this->post(route('subIdx'), [
            'sub' => $this->createUploadedFile("{$this->testFilesStoragePath}text/srt/empty.srt", "empty.sub"),
            'idx' => $this->createUploadedFile("{$this->testFilesStoragePath}text/srt/empty.srt", "empty.idx"),
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'sub' => __('validation.file_is_empty', ['attribute' => 'sub']),
                'idx' => __('validation.file_is_empty', ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_validates_uploaded_files()
    {
        $response = $this->post(route('subIdx'), [
            'sub' => UploadedFile::fake()->image('movie.sub'),
            'idx' => UploadedFile::fake()->image('text.idx'),
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'sub' => __('validation.subidx_invalid_sub_mime', ['attribute' => 'sub']),
                'idx' => __('validation.file_is_not_a_textfile',  ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_stores_valid_uploads_in_the_database_and_on_the_disk()
    {
        $this->withoutJobs();

        $subIdx = $this->postVobSub();

        $this->assertTrue(file_exists("{$subIdx->filePathWithoutExtension}.sub"), "Stored sub file does not exist");
        $this->assertTrue(file_exists("{$subIdx->filePathWithoutExtension}.idx"), "Stored idx file does not exist");
    }

    /** @test */
    function it_records_cache_hit_stats()
    {
        $this->setTestNow('2018-05-01 12:00:00');

        $this->withoutJobs();

        $subIdx = $this->postVobSub();

        $this->assertNull($subIdx->last_cache_hit);
        $this->assertSame(0, $subIdx->cache_hits);

        $subIdx = $this->postVobSub();

        $this->assertSame((string) now(), (string) $subIdx->last_cache_hit);
        $this->assertSame(1, $subIdx->cache_hits);

        $this->progressTimeInDays(5);

        $subIdx = $this->postVobSub();

        $this->assertSame((string) now(), (string) $subIdx->last_cache_hit);
        $this->assertSame(2, $subIdx->cache_hits);

        $this->assertSame(1, SubIdx::count());
    }

    /** @test */
    function it_creates_language_extract_jobs()
    {
        $this->expectsJobs(\App\Jobs\ExtractSubIdxLanguageJob::class);

        $this->postVobSub();

        $this->assertDatabaseHas('sub_idx_languages', ['sub_idx_id' => 1, 'index' => 0, 'language' => 'unknown']);
        $this->assertDatabaseHas('sub_idx_languages', ['sub_idx_id' => 1, 'index' => 1, 'language' => 'nl']);
    }

    /** @test */
    function it_extracts_languages()
    {
        $this->useMockVobSub2Srt();

        $subIdx = $this->postVobSub();

        $languages = $subIdx->languages()
            ->whereNull('error_message')
            ->whereNotNull('started_at')
            ->whereNotNull('finished_at')
            ->get();

        $this->assertSame(1, count($languages));

        $this->assertTrue(ends_with($languages[0]->fileName, '.srt'));

        $this->assertSame(1, StoredFile::count());

        foreach($languages->all() as $lang) {
            $this->assertTrue(file_exists($lang->filePath), "Extracted file does not exist ({$lang->filePath})");

            $this->assertTrue(filesize($lang->filepath) > 0, "Extracted file is empty");
        }
    }

    /** @test */
    function it_fires_an_event_after_extracting_a_language()
    {
        $this->useMockVobSub2Srt();

        $this->expectsEvents(\App\Events\ExtractingSubIdxLanguageChanged::class);

        $this->postVobSub();
    }
}
