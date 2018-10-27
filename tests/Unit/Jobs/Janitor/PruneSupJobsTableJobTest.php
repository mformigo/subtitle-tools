<?php

namespace Tests\Unit\Jobs\Janitor;

use App\Jobs\Janitor\PruneSupJobsTableJob;
use App\Models\SupJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PruneSupJobsTableJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->setTestNow('2018-05-01 12:00:00');
    }

    /** @test */
    function it_deletes_old_records_with_no_cache_hits()
    {
        $sup1 = factory(SupJob::class)->create(['last_cache_hit' => null, 'created_at' => now()]);
        $sup2 = factory(SupJob::class)->create(['last_cache_hit' => null, 'created_at' => now()->subDays(35)]);

        (new PruneSupJobsTableJob)->handle();
        $this->assertStillExists($sup1);
        $this->assertStillExists($sup2);

        $this->progressTimeInDays(35);

        (new PruneSupJobsTableJob)->handle();
        $this->assertStillExists($sup1);
        $this->assertDeleted($sup2);

        $this->progressTimeInDays(35);

        (new PruneSupJobsTableJob)->handle();
        $this->assertDeleted($sup1);
        $this->assertDeleted($sup2);
    }

    /** @test */
    function it_does_not_delete_old_records_with_recent_cache_hits()
    {
        $sup1 = factory(SupJob::class)->create([
            'created_at' => now()->subDays(100),
            'last_cache_hit' => now()->subDays(15),
        ]);

        $sup2 = factory(SupJob::class)->create([
            'created_at' => now()->subDays(100),
            'last_cache_hit' => now()->subDays(45),
        ]);

        (new PruneSupJobsTableJob)->handle();
        $this->assertStillExists($sup1);
        $this->assertStillExists($sup2);

        $this->progressTimeInDays(35);

        (new PruneSupJobsTableJob)->handle();
        $this->assertStillExists($sup1);
        $this->assertDeleted($sup2);

        $this->progressTimeInDays(35);

        (new PruneSupJobsTableJob)->handle();
        $this->assertDeleted($sup1);
        $this->assertDeleted($sup2);
    }

    /** @test */
    function it_deletes_old_records_with_old_cache_hits()
    {
        $subIdx = factory(SupJob::class)->create([
            'created_at' => now()->subDays(200),
            'last_cache_hit' => now()->subDays(100),
        ]);

        (new PruneSupJobsTableJob)->handle();

        $this->assertDeleted($subIdx);
    }

    /** @test */
    function it_does_not_delete_recent_records_with_recent_cache_hits()
    {
        $subIdx = factory(SupJob::class)->create([
            'created_at' => now()->subDays(30),
            'last_cache_hit' => now()->subDays(20),
        ]);

        (new PruneSupJobsTableJob)->handle();

        $this->assertStillExists($subIdx);
    }

    /** @test */
    function it_does_not_delete_recent_records()
    {
        $sup1 = factory(SupJob::class)->create([
            'created_at' => now(),
            'last_cache_hit' => null,
        ]);

        $sup2 = factory(SupJob::class)->create([
            'created_at' => now()->subDays(30),
            // it should be impossible for "last_cache_hit" to be before "created_at"
            'last_cache_hit' => now()->subDays(100),
        ]);

        (new PruneSupJobsTableJob)->handle();

        $this->assertStillExists($sup1);
        $this->assertStillExists($sup2);
    }

    private function assertStillExists($model)
    {
        $record = SupJob::find($model->id);

        $this->assertInstanceOf(SupJob::class, $record, 'SupJob was unexpectedly deleted');
    }

    private function assertDeleted($model)
    {
        $record = SupJob::find($model->id);

        $this->assertNull($record, 'SupJob still exists');
    }
}
