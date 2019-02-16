<?php

namespace Tests\Unit\Models;

use App\Models\DiskUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiskUsageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_calculates_the_disk_usage_percentage()
    {
        $diskUsage = factory(DiskUsage::class)->create([
            'total_used' => 23456789,
            'total_size' => 45678911,
        ]);
        $this->assertSame(51, $diskUsage->total_usage_percentage);

        $diskUsage = factory(DiskUsage::class)->create([
            'total_used' => 0,
            'total_size' => 45678911,
        ]);
        $this->assertSame(0, $diskUsage->total_usage_percentage);

        $diskUsage = factory(DiskUsage::class)->create([
            'total_used' => 45678911,
            'total_size' => 45678911,
        ]);
        $this->assertSame(100, $diskUsage->total_usage_percentage);

        $diskUsage = factory(DiskUsage::class)->create([
            'total_used' => 0,
            'total_size' => 0,
        ]);
        $this->assertSame(0, $diskUsage->total_usage_percentage);
    }
}
