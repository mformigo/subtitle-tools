<?php

namespace Tests\Unit\Controllers\Api;

use App\Jobs\ExtractSubIdxLanguageJob;
use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubIdxControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $snapshotDirectory = 'api';

    /** @test */
    function it_returns_queued_language_extract_information()
    {
        $sub = $this->createUploadedFile('sub-idx/error-and-nl.sub');
        $idx = $this->createUploadedFile('sub-idx/error-and-nl.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $response = $this->getLanguages($subIdx)->assertStatus(200);

        $this->assertMatchesJsonSnapshot($response);
    }

    /** @test */
    function it_returns_finished_and_failed_language_extract_information()
    {
        $subIdx = factory(SubIdx::class)->create(['url_key' => 'abc123']);

        $subIdx->languages()->saveMany([
            factory(SubIdxLanguage::class)->states(DETERMINISTIC, 'idle')->make(),
            factory(SubIdxLanguage::class)->states(DETERMINISTIC, 'queued')->make(),
            factory(SubIdxLanguage::class)->states(DETERMINISTIC, 'processing')->make(),
            factory(SubIdxLanguage::class)->states(DETERMINISTIC, 'failed')->make(),
            factory(SubIdxLanguage::class)->states(DETERMINISTIC, 'finished')->make(),
        ]);

        $response = $this->getLanguages($subIdx)->assertStatus(200);

        $this->assertMatchesJsonSnapshot($response);
    }

    /** @test */
    function it_can_queue_a_job_to_extract_a_language()
    {
        Queue::fake();

        $sub = $this->createUploadedFile('sub-idx/error-and-nl.sub');
        $idx = $this->createUploadedFile('sub-idx/error-and-nl.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $language = $subIdx->languages->first();

        $this->extractLanguage($subIdx, $language)->assertStatus(200);

        Queue::assertPushed(ExtractSubIdxLanguageJob::class, function (ExtractSubIdxLanguageJob $job) use ($language) {
            return $job->subIdxLanguage->id === $language->id;
        });

        Queue::assertPushedOn('B200', ExtractSubIdxLanguageJob::class);

        $this->assertNotNull($language->refresh()->queued_at);
    }

    /** @test */
    function it_does_not_extract_when_the_url_key_is_wrong()
    {
        $sub = $this->createUploadedFile('sub-idx/error-and-nl.sub');
        $idx = $this->createUploadedFile('sub-idx/error-and-nl.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $language = $subIdx->languages->first();

        $this->extractLanguage('asdjadjal', $language)->assertStatus(404);
    }

    /** @test */
    function it_only_starts_extracting_idle_languages()
    {
        $sub = $this->createUploadedFile('sub-idx/error-and-nl.sub');
        $idx = $this->createUploadedFile('sub-idx/error-and-nl.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $language = $subIdx->languages->first();

        $language->update(['queued_at' => now()]);

        $this->extractLanguage($subIdx, $language)->assertStatus(404);
    }

    private function getLanguages($urlKey)
    {
        if ($urlKey instanceof SubIdx) {
            $urlKey = $urlKey->url_key;
        }

        return $this->json('GET', route('api.subIdx.languages', $urlKey));
    }

    private function extractLanguage($urlKey, $languageId)
    {
        if ($urlKey instanceof SubIdx) {
            $urlKey = $urlKey->url_key;
        }

        if ($languageId instanceof SubIdxLanguage) {
            $languageId = $languageId->id;
        }

        return $this->json('POST', route('api.subIdx.post', [$urlKey, $languageId]));
    }
}
