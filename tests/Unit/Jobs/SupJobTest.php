<?php

namespace Tests\Unit\Jobs;

use App\Models\StoredFile;
use App\Models\SupJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupJobTest extends TestCase
{
    use RefreshDatabase;

    private function makeSupJob($inputFilePath, $ocrLanguage = 'eng')
    {
        $inputFilePath = $this->testFilesStoragePath . ltrim($inputFilePath, '/');

        if (! file_exists($inputFilePath)) {
            $this->fail('File does not exist: '.$inputFilePath);
        }

        $storedFile = StoredFile::getOrCreate($inputFilePath);

        return SupJob::create([
            'url_key'              => generate_url_key(),
            'input_file_hash'      => '123',
            'ocr_language'         => $ocrLanguage,
            'input_stored_file_id' => $storedFile->id,
            'original_name'        => basename($inputFilePath),
        ]);
    }

    /** @test */
    function it_converts_a_sup_to_srt()
    {
        $supJob = $this->makeSupJob('sup/three-english-cues.sup', 'eng');

        $supJob->dispatchJob();

        $supJob = SupJob::findOrFail($supJob->id);

        $this->assertNull($supJob->error_message);

        $this->assertMatchesFileSnapshot($supJob->outputStoredFile);
    }
}
