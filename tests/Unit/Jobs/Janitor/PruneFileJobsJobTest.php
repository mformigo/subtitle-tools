<?php

namespace Tests\Unit\Jobs\Janitor;

use App\Jobs\Janitor\PruneFileJobsJob;
use App\Models\FileGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PruneFileJobsJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_deletes_old_file_jobs()
    {
        $fileGroup1 = factory(FileGroup::class)->create(['created_at' => now()]);
        $fileGroup2 = factory(FileGroup::class)->create(['created_at' => now()->subDays(4)]);

        (new PruneFileJobsJob)->handle();
        $this->assertModelExists($fileGroup1);
        $this->assertModelDoesntExist($fileGroup2);

        (new PruneFileJobsJob)->handle();
        $this->assertModelExists($fileGroup1);

        $this->progressTimeInDays(4);

        (new PruneFileJobsJob)->handle();
        $this->assertModelDoesntExist($fileGroup1);
    }
}
