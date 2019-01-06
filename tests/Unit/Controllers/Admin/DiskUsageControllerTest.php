<?php

namespace Tests\Unit\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiskUsageControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_disk_usage_page()
    {
        $this->adminLogin()
            ->get(route('admin.diskUsage.index'))
            ->assertStatus(200);
    }
}
