<?php

namespace Tests\Unit\Controllers\Admin;

use App\Models\DiskUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiskUsageControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_disk_usage_page()
    {
        factory(DiskUsage::class)->create(['created_at' => now()->subHours(5)]);
        factory(DiskUsage::class)->create(['created_at' => now()->subHours(4)]);
        factory(DiskUsage::class)->create(['created_at' => now()->subHours(3)]);
        factory(DiskUsage::class)->create(['created_at' => now()->subHours(2)]);
        factory(DiskUsage::class)->create(['created_at' => now()->subHours(1)]);

        $this->adminLogin()
            ->get(route('admin.diskUsage.index'))
            ->assertStatus(200);
    }

    /** @test */
    function it_can_show_the_disk_usage_page_with_an_empty_database()
    {
        $this->adminLogin()
            ->get(route('admin.diskUsage.index'))
            ->assertStatus(200);
    }
}
