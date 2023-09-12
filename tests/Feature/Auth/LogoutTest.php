<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $this->postJson('/api/auth/logout', [], ['Authorization' => 'Bearer ' . $token])->assertStatus(200);
        $this->assertGuest();
    }

    /**
     * @test
     */
    public function guest_can_not_logout()
    {
        $this->postJson('/api/auth/logout')->assertStatus(401);
    }
}
