<?php

namespace Tests\Unit\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_the_index()
    {
        $this->adminLogin()
            ->get(route('admin.sup.index'))
            ->assertStatus(200);
    }
}
