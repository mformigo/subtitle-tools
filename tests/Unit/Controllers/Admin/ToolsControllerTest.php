<?php

namespace Tests\Unit\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_disk_usage_page()
    {
        $this->adminLogin()
            ->get(route('admin.tools.index'))
            ->assertStatus(200);
    }
}
