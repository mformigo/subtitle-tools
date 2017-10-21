<?php

namespace Tests\Unit;

use App\Jobs\SupToSrtJob;
use App\Models\StoredFile;
use App\Models\SupGroup;
use App\Models\SupJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupToSrtJobTest extends TestCase
{
    use RefreshDatabase;

    private function makeSupJob($inputFilePath)
    {
        if(! file_exists($inputFilePath)) {
            $this->fail('File does not exist: '.$inputFilePath);
        }

        $storedFile = StoredFile::getOrCreate($inputFilePath);

        $supGroup = SupGroup::create(['url_key' => str_random()]);

        return SupJob::create([
            'sup_group_id' => $supGroup->id,
            'input_stored_file_id' => $storedFile->id,
            'original_name' => basename($inputFilePath),
        ]);
    }

    /** @test */
    function it_measures_queue_times()
    {
        $supJob = $this->makeSupJob($this->testFilesStoragePath.'Sup/hddvd01-mini.sup');

        dispatch(
            new SupToSrtJob($supJob)
        );

        $supJob = SupJob::findOrFail($supJob->id);

        $this->assertNotNull($supJob->started_at);
        $this->assertNotNull($supJob->finished_at);
        $this->assertNotNull($supJob->queue_time);
        $this->assertNotNull($supJob->work_time);
    }
}