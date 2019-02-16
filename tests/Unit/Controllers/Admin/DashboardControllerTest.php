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
        $this->getDashboard()
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    /** @test */
    function it_can_show_with_a_seeded_database()
    {
        $this->seed();

        $this->adminLogin()
            ->getDashboard()
            ->assertStatus(200);
    }

    /** @test */
    function it_can_show_the_dashboard_with_an_empty_database()
    {
        $this->adminLogin()
            ->getDashboard()
            ->assertStatus(200);
    }

    private function getDashboard()
    {
        return $this->get(route('admin.dashboard.index'));
    }
}
