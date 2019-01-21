<?php

namespace Tests\Unit\Jobs;

use App\Jobs\RandomizeSubIdxUrlKeysJob;
use App\Models\SubIdx;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RandomizeSubIdxUrlKeysJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_changes_the_url_key_of_stale_sub_idx_models()
    {
        Carbon::setTestNow('2019-01-20 12:00:00');

        $s1 = factory(SubIdx::class)->create(['last_cache_hit' => null, 'updated_at' => now()->subHours(40)]);
        $s2 = factory(SubIdx::class)->create(['last_cache_hit' => null, 'updated_at' => now()->subHours(20)]);
        $s3 = factory(SubIdx::class)->create(['last_cache_hit' => null, 'updated_at' => now()]);

        (new RandomizeSubIdxUrlKeysJob)->handle();

        $this->assertChangedUrlKey($s1);
        $this->assertDidNotChangeUrlKey($s2);
        $this->assertDidNotChangeUrlKey($s3);

        $this->progressTimeInHours(20);
        (new RandomizeSubIdxUrlKeysJob)->handle();

        $this->assertDidNotChangeUrlKey($s1);
        $this->assertChangedUrlKey($s2);
        $this->assertDidNotChangeUrlKey($s3);

        $this->progressTimeInHours(20);
        (new RandomizeSubIdxUrlKeysJob)->handle();

        $this->assertChangedUrlKey($s1);
        $this->assertDidNotChangeUrlKey($s2);
        $this->assertChangedUrlKey($s3);
    }

    /** @test */
    function it_does_not_change_the_url_key_if_it_recently_had_a_cache_hit()
    {
        Carbon::setTestNow('2019-01-20 12:00:00');

        $s1 = factory(SubIdx::class)->create(['last_cache_hit' => now()->subMinutes(10), 'updated_at' => now()->subHours(100)]);
        $s2 = factory(SubIdx::class)->create(['last_cache_hit' => now()->subMinutes(40), 'updated_at' => now()->subHours(100)]);

        (new RandomizeSubIdxUrlKeysJob)->handle();

        $this->assertDidNotChangeUrlKey($s1);
        $this->assertChangedUrlKey($s2);

        $this->progressTimeInHours(1);
        (new RandomizeSubIdxUrlKeysJob)->handle();

        $this->assertChangedUrlKey($s1);
        $this->assertDidNotChangeUrlKey($s2);
    }

    private function assertChangedUrlKey(SubIdx $subIdx)
    {
        $fresh = SubIdx::findOrFail($subIdx->id);

        $this->assertNotSame($subIdx->url_key, $fresh->url_key);

        $subIdx->refresh();
    }

    private function assertDidNotChangeUrlKey(SubIdx $subIdx)
    {
        $fresh = SubIdx::findOrFail($subIdx->id);

        $this->assertSame($subIdx->url_key, $fresh->url_key);

        $subIdx->refresh();
    }
}
