<?php

namespace Tests\Unit\Controllers;

use App\Models\StoredFile;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupControllerTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles;

    /** @test */
    function it_rejects_files_that_are_not_sup()
    {
        $this->post(route('sup'), [
            'subtitle'    => $this->createUploadedFile('text/ass/three-cues.ass'),
            'ocrLanguage' => 'eng',
        ])
        ->assertStatus(302)
        ->assertSessionHasErrors(['subtitle' => __('validation.not_a_valid_sup_file')]);
    }

    /** @test */
    function it_converts_sup_to_srt()
    {
        $this->post(route('sup'), [
            'subtitle'    => $this->createUploadedFile('sup/three-english-cues.sup'),
            'ocrLanguage' => 'eng',
        ])
        ->assertStatus(302);

        $outputFile = StoredFile::findOrFail(2);

        $lines = read_lines($outputFile);

        $this->assertMatchesSnapshot($lines);
    }
}
