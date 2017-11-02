<?php

namespace Tests\Unit;

use App\Jobs\SupToSrtJob;
use App\Models\StoredFile;
use App\Models\SupJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SjorsO\TextFile\Facades\TextFileReader;
use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

class SupToSrtJobTest extends TestCase
{
    use RefreshDatabase, MatchesSnapshots;

    private function makeSupJob($inputFilePath, $ocrLanguage = 'auto_detect')
    {
        $inputFilePath = $this->testFilesStoragePath . ltrim($inputFilePath, '/');

        if(! file_exists($inputFilePath)) {
            $this->fail('File does not exist: '.$inputFilePath);
        }

        $storedFile = StoredFile::getOrCreate($inputFilePath);

        return SupJob::create([
            'url_key' => str_random(16),
            'ocr_language' => $ocrLanguage,
            'input_stored_file_id' => $storedFile->id,
            'original_name' => basename($inputFilePath),
        ]);
    }

    /** @test */
    function it_measures_queue_times()
    {
        $supJob = $this->makeSupJob('Sup/hddvd01-mini.sup');

        dispatch(
            new SupToSrtJob($supJob)
        );

        $supJob = SupJob::findOrFail($supJob->id);

        $this->assertNotNull($supJob->started_at);
        $this->assertNotNull($supJob->finished_at);
        $this->assertNotNull($supJob->queue_time);
        $this->assertNotNull($supJob->work_time);
    }

    /** @test */
    function it_converts_a_sup_to_srt()
    {
        $supJob = $this->makeSupJob('Sup/hddvd01-mini.sup', 'jpn');

        dispatch(
            new SupToSrtJob($supJob)
        );

        $supJob = SupJob::findOrFail($supJob->id);

        $this->assertNull($supJob->error_message);

        $outputLines = TextFileReader::getLines($supJob->outputStoredFile->file_path);

        $this->assertMatchesSnapshot($outputLines);
    }
}