<?php

namespace Tests\Unit\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_redirects_guests()
    {
        $this->get(route('admin'))
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    /** @test */
    function it_allows_logged_in_users()
    {
        $admin = User::findOrFail(1);

        $this->actingAs($admin)
            ->get(route('admin'))
            ->assertStatus(200);
    }
}
