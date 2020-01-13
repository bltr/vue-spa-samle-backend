<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefreshTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_refresh_access_token()
    {
        $user = factory(User::class)->create();
        $token = auth()->login($user);

        $response = $this->postJson('/api/auth/refresh', [], ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('access_token', $json);
        $this->assertIsString($json['access_token']);
        $this->assertNotEquals($token, $json['access_token']);
        $this->assertAuthenticated();
    }

    /**
     * @test
     */
    public function not_authenticated_user_cant_refresh_access_token()
    {
        $user = factory(User::class)->create();

        $this->postJson('/api/auth/refresh')
            ->assertStatus(401);
    }
}
