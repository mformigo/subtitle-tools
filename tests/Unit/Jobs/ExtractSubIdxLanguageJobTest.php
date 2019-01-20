<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ExtractSubIdxLanguageJob;
use App\Models\StoredFile;
use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use App\Support\Facades\VobSub2Srt;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ExtractSubIdxLanguageJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_extracts_a_language_from_a_vobsub()
    {
        Carbon::setTestNow('2019-01-19 12:00:00');

        VobSub2Srt::fake()->outputSrt();

        $language = $this->createSubIdxLanguage();

        (new ExtractSubIdxLanguageJob($language))->handle();

        $this->assertNow($language->started_at);
        $this->assertNow($language->finished_at);
        $this->assertNull($language->error_message);

        $this->assertInstanceOf(StoredFile::class, $language->outputStoredFile);
    }

    /** @test */
    function it_handles_when_vobsub2srt_outputs_nothing()
    {
        Carbon::setTestNow('2019-01-19 12:00:00');

        VobSub2Srt::fake()->outputNothing();

        $language = $this->createSubIdxLanguage();

        (new ExtractSubIdxLanguageJob($language))->handle();

        $this->assertNow($language->started_at);
        $this->assertNow($language->finished_at);
        $this->assertSame('messages.subidx_no_vobsub2srt_output_file', $language->error_message);
    }

    /** @test */
    function it_handles_when_vobsub2srt_outputs_an_empty_file()
    {
        Carbon::setTestNow('2019-01-19 12:00:00');

        VobSub2Srt::fake()->outputEmptyFile();

        $language = $this->createSubIdxLanguage();

        (new ExtractSubIdxLanguageJob($language))->handle();

        $this->assertNow($language->started_at);
        $this->assertNow($language->finished_at);
        $this->assertSame('messages.subidx_empty_vobsub2srt_output_file', $language->error_message);
    }

    /** @test */
    function it_handles_when_vobsub2srt_outputs_an_srt_with_no_dialogue()
    {
        Carbon::setTestNow('2019-01-19 12:00:00');

        VobSub2Srt::fake()->outputSrtWithNoDialogue();

        $language = $this->createSubIdxLanguage();

        (new ExtractSubIdxLanguageJob($language))->handle();

        $this->assertNow($language->started_at);
        $this->assertNow($language->finished_at);
        $this->assertSame('messages.subidx_vobsub2srt_output_file_only_empty_cues', $language->error_message);
    }

    /** @test */
    function it_saves_the_start_time_when_the_job_starts()
    {
        Carbon::setTestNow('2019-01-19 12:00:00');

        VobSub2Srt::fake()->outputThrowException();

        $language = $this->createSubIdxLanguage();

        try {
            (new ExtractSubIdxLanguageJob($language))->handle();
        } catch (RuntimeException $e) {
            $this->assertNow($language->refresh()->started_at);

            return;
        }

        $this->fail('No exception thrown');
    }

    private function createSubIdxLanguage($path = 'sub-idx/error-and-nl'): SubIdxLanguage
    {
        $sub = $this->createUploadedFile($path.'.sub');
        $idx = $this->createUploadedFile($path.'.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $language = $subIdx->languages->first();

        $language->update(['queued_at' => now()->subSeconds(10)]);

        return $language;
    }
}
