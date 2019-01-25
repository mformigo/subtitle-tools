<?php

namespace Tests\Unit\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubIdxControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_index()
    {
        $this->seed();

        $this->adminLogin()
            ->get(route('admin.subIdx.index'))
            ->assertStatus(200);
    }
}
