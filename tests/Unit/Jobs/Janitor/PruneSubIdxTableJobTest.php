<?php

namespace Tests\Unit\Jobs\Janitor;

use App\Jobs\Janitor\PruneSubIdxTableJob;
use App\Models\SubIdx;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PruneSubIdxTableJobTest extends TestCase
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
        $subIdx1 = factory(SubIdx::class)->create(['last_cache_hit' => null, 'created_at' => now()]);
        $subIdx2 = factory(SubIdx::class)->create(['last_cache_hit' => null, 'created_at' => now()->subDays(35)]);

        (new PruneSubIdxTableJob)->handle();
        $this->assertStillExists($subIdx1);
        $this->assertStillExists($subIdx2);

        $this->progressTimeInDays(35);

        (new PruneSubIdxTableJob)->handle();
        $this->assertStillExists($subIdx1);
        $this->assertDeleted($subIdx2);

        $this->progressTimeInDays(35);

        (new PruneSubIdxTableJob)->handle();
        $this->assertDeleted($subIdx1);
        $this->assertDeleted($subIdx2);
    }

    /** @test */
    function it_does_not_delete_old_records_with_recent_cache_hits()
    {
        $subIdx1 = factory(SubIdx::class)->create([
            'created_at' => now()->subDays(100),
            'last_cache_hit' => now()->subDays(15),
        ]);

        $subIdx2 = factory(SubIdx::class)->create([
            'created_at' => now()->subDays(100),
            'last_cache_hit' => now()->subDays(45),
        ]);

        (new PruneSubIdxTableJob)->handle();
        $this->assertStillExists($subIdx1);
        $this->assertStillExists($subIdx2);

        $this->progressTimeInDays(35);

        (new PruneSubIdxTableJob)->handle();
        $this->assertStillExists($subIdx1);
        $this->assertDeleted($subIdx2);

        $this->progressTimeInDays(35);

        (new PruneSubIdxTableJob)->handle();
        $this->assertDeleted($subIdx1);
        $this->assertDeleted($subIdx2);
    }

    /** @test */
    function it_deletes_old_records_with_old_cache_hits()
    {
        $subIdx = factory(SubIdx::class)->create([
            'created_at' => now()->subDays(200),
            'last_cache_hit' => now()->subDays(100),
        ]);

        (new PruneSubIdxTableJob)->handle();

        $this->assertDeleted($subIdx);
    }

    /** @test */
    function it_does_not_delete_recent_records_with_recent_cache_hits()
    {
        $subIdx = factory(SubIdx::class)->create([
            'created_at' => now()->subDays(30),
            'last_cache_hit' => now()->subDays(20),
        ]);

        (new PruneSubIdxTableJob)->handle();

        $this->assertStillExists($subIdx);
    }

    /** @test */
    function it_does_not_delete_recent_records()
    {
        $subIdx1 = factory(SubIdx::class)->create([
            'created_at' => now(),
            'last_cache_hit' => null,
        ]);

        $subIdx2 = factory(SubIdx::class)->create([
            'created_at' => now()->subDays(30),
            // it should be impossible for "last_cache_hit" to be before "created_at"
            'last_cache_hit' => now()->subDays(100),
        ]);

        (new PruneSubIdxTableJob)->handle();

        $this->assertStillExists($subIdx1);
        $this->assertStillExists($subIdx2);
    }

    private function assertStillExists($model)
    {
        $record = SubIdx::find($model->id);

        $this->assertInstanceOf(SubIdx::class, $record, 'Sub/idx was unexpectedly deleted');
    }

    private function assertDeleted($model)
    {
        $record = SubIdx::find($model->id);

        $this->assertNull($record, 'Sub/idx still exists');
    }
}
