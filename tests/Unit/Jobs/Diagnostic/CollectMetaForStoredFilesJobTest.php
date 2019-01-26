<?php

namespace Tests\Unit\Jobs\Diagnostic;

use App\Jobs\Diagnostic\CollectMetaForStoredFilesJob;
use App\Jobs\Diagnostic\CollectStoredFileMetaJob;
use App\Models\StoredFileMeta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CollectMetaForStoredFilesJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_dispatches_jobs_for_stored_files_without_meta()
    {
        Queue::fake();

        $s1 = $this->createStoredFile();
        $s2 = $this->createStoredFile();
        $s3 = $this->createStoredFile();

        $s2->meta()->save(
            factory(StoredFileMeta::class)->make()
        );

        (new CollectMetaForStoredFilesJob)->handle();

        Queue::assertPushed(CollectStoredFileMetaJob::class, 2);

        Queue::assertPushedOn('low-fast', CollectStoredFileMetaJob::class, function (CollectStoredFileMetaJob $job) use ($s1) {
            return $job->storedFile->id === $s1->id;
        });

        Queue::assertPushedOn('low-fast', CollectStoredFileMetaJob::class, function (CollectStoredFileMetaJob $job) use ($s3) {
            return $job->storedFile->id === $s3->id;
        });
    }
}
