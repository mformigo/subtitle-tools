<?php

namespace Tests\Unit\Controllers;

use App\Models\StoredFile;
use App\Models\SupJob;
use App\Models\SupStats;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_rejects_files_that_are_not_sup()
    {
        $this->postSup([
            'subtitle' => $this->createUploadedFile('text/ass/three-cues.ass'),
            'ocrLanguage' => 'eng',
        ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['subtitle' => __('validation.not_a_valid_sup_file')]);
    }

    /** @test */
    function it_converts_sup_to_srt()
    {
        Carbon::setTestNow('2018-05-01 12:00:00');

        $this->postSup($postData = [
            'subtitle' => $this->createUploadedFile('sup/three-english-cues.sup'),
            'ocrLanguage' => 'eng',
        ])
        ->assertSessionHasNoErrors()
        ->assertStatus(302);

        $supJob = SupJob::findOrFail(1);

        $this->assertNull($supJob->last_cache_hit);
        $this->assertSame(0, $supJob->cache_hits);

        $outputFile = StoredFile::findOrFail(2);

        $this->assertMatchesSnapshot(
            read_lines($outputFile)
        );

        $this->progressTimeInDays(5);

        $originalUpdatedAt = (string) $supJob->updated_at;

        // post the same file + language again
        $this->postSup($postData)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('sup.show', $supJob->url_key));

        $supJob->refresh();
        $this->assertSame((string) now(), (string) $supJob->last_cache_hit);
        $this->assertSame(1, $supJob->cache_hits);

        // It should not update the "updated_at" when retrieving the SupJob from cache.
        $this->assertSame($originalUpdatedAt, (string) $supJob->updated_at);

        $this->assertSame(1, SupJob::count());
    }

    /** @test */
    function it_records_sup_statistics()
    {
        $this->postSup([
            'subtitle' => $this->createUploadedFile('sup/three-english-cues.sup'),
            'ocrLanguage' => 'eng',
        ])
        ->assertSessionHasNoErrors()
        ->assertStatus(302);

        $this->assertSame(1, SupStats::count());

        // The cron also calls this static "today" method.
        $supStats = SupStats::today();

        $this->assertSame(1, $supStats->bluray_sup_count);
        $this->assertSame(0, $supStats->dvd_sup_count);
        $this->assertSame(0, $supStats->hddvd_sup_count);

        $this->assertSame(32753, $supStats->total_size);
        $this->assertSame(3, $supStats->images_ocrd_count);
        $this->assertTrue($supStats->milliseconds_spent_ocring > 100);
    }

    /** @test */
    function it_redirects_get_method_downloads_to_the_result_route()
    {

    }

    private function postSup(array $data)
    {
        return $this->post(route('sup'), $data);
    }
}
