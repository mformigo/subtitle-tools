<?php

namespace Tests\Feature;

use App\Models\StoredFile;
use SjorsO\TextFile\Facades\TextFileReader;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupControllerTest extends TestCase
{
    use RefreshDatabase, CreatesUploadedFiles;

    /** @test */
    function it_converts_sup_to_srt()
    {
        $this->post(route('sup'), [
            'subtitle' => $this->createUploadedFile($this->testFilesStoragePath.'/Sup/three-english-cues.sup'),
            'ocrLanguage' => 'eng',
        ])
        ->assertStatus(302);

        $outputFile = StoredFile::findOrFail(2)->file_path;

        $lines = TextFileReader::getLines($outputFile);

        $this->assertMatchesSnapshot($lines);
    }
}
