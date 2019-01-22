<?php

namespace Tests\Unit\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_redirects_guests()
    {
        $this->get(route('admin.dashboard.index'))
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    /** @test */
    function it_allows_logged_in_users()
    {
        $this->adminLogin()
            ->get(route('admin.dashboard.index'))
            ->assertStatus(200);
    }

    /** @test */
    function it_can_show_with_a_seeded_database()
    {
        $this->seed();

        $this->adminLogin()
            ->get(route('admin.dashboard.index'))
            ->assertStatus(200);
    }
}
