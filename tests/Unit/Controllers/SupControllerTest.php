<?php

namespace Tests\Unit\Controllers;

use App\Models\StoredFile;
use App\Models\SupJob;
use Carbon\Carbon;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupControllerTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles;

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

        // post the same file + language again
        $this->postSup($postData)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('sup.show', $supJob->url_key));

        $supJob->refresh();
        $this->assertSame((string) now(), (string) $supJob->last_cache_hit);
        $this->assertSame(1, $supJob->cache_hits);

        $this->assertSame(1, SupJob::count());
    }

    private function postSup(array $data)
    {
        return $this->post(route('sup'), $data);
    }
}
