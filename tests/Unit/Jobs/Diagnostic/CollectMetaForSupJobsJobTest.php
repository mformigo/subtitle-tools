<?php

namespace Tests\Unit\Jobs\Diagnostic;

use App\Jobs\Diagnostic\CollectMetaForSupJobsJob;
use App\Jobs\Diagnostic\CollectSupMetaJob;
use App\Models\Diagnostic\SupJobMeta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CollectMetaForSupJobsJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_dispatches_jobs_for_stored_files_without_meta()
    {
        Queue::fake();

        $sup1 = $this->createSupJob(['finished_at' => now()]);
        $sup2 = $this->createSupJob(['finished_at' => now()]);
        $sup3 = $this->createSupJob(['finished_at' => now()]);

        $sup2->meta()->save(
            factory(SupJobMeta::class)->make()
        );

        (new CollectMetaForSupJobsJob)->handle();

        Queue::assertPushed(CollectSupMetaJob::class, 2);

        Queue::assertPushedOn('A500', CollectSupMetaJob::class, function (CollectSupMetaJob $job) use ($sup1) {
            return $job->supJob->id === $sup1->id;
        });

        Queue::assertPushedOn('A500', CollectSupMetaJob::class, function (CollectSupMetaJob $job) use ($sup3) {
            return $job->supJob->id === $sup3->id;
        });
    }
}
