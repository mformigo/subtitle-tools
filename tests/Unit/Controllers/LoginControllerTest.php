<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_login_an_admin()
    {
        $user = factory(User::class)->create([
            'username' => 'Admin',
            'password' => bcrypt('secret'),
        ]);

        $this->assertGuest();

        $this->postLogin(['username' => 'Admin', 'password' => 'secret'])
            ->assertStatus(302)
            ->assertRedirect(route('admin.dashboard.index'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    function it_can_fail_a_login()
    {
        $user = factory(User::class)->create([
            'username' => 'Admin',
            'password' => bcrypt('secret'),
        ]);

        $this->assertGuest();

        $this->postLogin(['username' => 'Admin', 'password' => 'wrong'])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->assertGuest();
    }

    /** @test */
    function it_can_logout()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->postLogout()
            ->assertStatus(302);

        $this->assertGuest();
    }

    private function postLogin($data)
    {
        return $this->post(route('login.post'), $data);
    }

    private function postLogout()
    {
        return $this->post(route('logout'));
    }
}
