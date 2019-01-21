<?php

namespace Tests\Unit\Models;

use App\Jobs\ExtractSubIdxLanguageJob;
use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubIdxTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_create_a_subidx_from_uploaded_files()
    {
        $this->doesntExpectJobs(ExtractSubIdxLanguageJob::class);

        Carbon::setTestNow('2019-01-19 12:00:00');

        $sub = $this->createUploadedFile('sub-idx/error-and-nl.sub');
        $idx = $this->createUploadedFile('sub-idx/error-and-nl.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $this->assertSame('sub-idx/2019-18/1547895600-ca9b27da96b2/', $subIdx->store_directory);

        $this->assertNotNull($subIdx->url_key);

        Storage::assertExists($subIdx->store_directory.$subIdx->filename.'.sub');
        $this->assertFileExists($subIdx->file_path_without_extension.'.sub');

        Storage::assertExists($subIdx->store_directory.$subIdx->filename.'.idx');
        $this->assertFileExists($subIdx->file_path_without_extension.'.idx');
    }

    /** @test */
    function it_retrieves_subidxes_from_cache()
    {
        $sub = $this->createUploadedFile('sub-idx/error-and-nl.sub');
        $idx = $this->createUploadedFile('sub-idx/error-and-nl.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $subIdx->refresh();

        $originalUpdatedAt = (string) $subIdx->updated_at;

        $this->assertNull($subIdx->last_cache_hit);
        $this->assertSame(0, $subIdx->cache_hits);

        $this->progressTimeInHours(1);

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $this->assertNow($subIdx->last_cache_hit);
        $this->assertSame(1, $subIdx->cache_hits);

        // It should not updated the "updated_at" column when registering cache hits.
        $this->assertSame($originalUpdatedAt, (string) $subIdx->refresh()->updated_at);

        $this->assertCount(1, SubIdx::all());
    }

    /** @test */
    function the_subidx_languages_are_pending()
    {
        Carbon::setTestNow('2019-01-19 12:00:00');

        $sub = $this->createUploadedFile('sub-idx/error-and-nl.sub');
        $idx = $this->createUploadedFile('sub-idx/error-and-nl.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $this->assertCount(2, $subIdx->languages);

        /** @var SubIdxLanguage $language */
        $language = $subIdx->languages->first();

        $this->assertNull($language->error_message);
        $this->assertNull($language->queued_at);
        $this->assertNull($language->started_at);
        $this->assertNull($language->finished_at);

        $this->assertFalse($language->is_queued);
        $this->assertFalse($language->is_processing);
    }

    /** @test */
    function it_does_not_set_an_url_key_for_unreadable_subidxes()
    {
        $sub = $this->createUploadedFile('sup/three-english-cues.sup');
        $idx = $this->createUploadedFile('sub-idx/error-and-nl.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $this->assertFalse($subIdx->is_readable);

        $this->assertNull($subIdx->url_key);

        $this->assertCount(0, $subIdx->languages);
    }

    /** @test */
    function it_starts_extracting_immediately_if_there_is_only_one_language()
    {
        Carbon::setTestNow('2019-01-19 12:00:00');

        Queue::fake();

        $sub = $this->createUploadedFile('sub-idx/only-danish.sub');
        $idx = $this->createUploadedFile('sub-idx/only-danish.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $this->assertCount(1, $subIdx->languages);

        $language = $subIdx->languages->first();

        $this->assertNow($language->queued_at);

        Queue::assertPushed(ExtractSubIdxLanguageJob::class, function (ExtractSubIdxLanguageJob $job) use ($language) {
            return $job->subIdxLanguage->id === $language->id;
        });

        Queue::assertPushedOn('sub-idx', ExtractSubIdxLanguageJob::class);
    }
}
