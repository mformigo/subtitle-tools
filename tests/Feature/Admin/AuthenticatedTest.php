<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_redirects_guests()
    {
        $response = $this->get(route('admin'));

        $response->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    /** @test */
    function it_allows_logged_in_users()
    {
        $this->actingAs(User::findOrFail(1));

        $this->get(route('admin'))
            ->assertStatus(200);
    }
}
