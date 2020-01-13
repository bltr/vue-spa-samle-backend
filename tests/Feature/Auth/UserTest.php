<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_get_his_profile_info()
    {
        $user = factory(User::class)->create();
        $token = auth()->login($user);

        $this->getJson('/api/auth/user', ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(201)
            ->assertJson($user->toArray());
    }

    /**
     * @test
     */
    public function guest_cant_get_profile_info()
    {
        $this->getJson('/api/auth/user')->assertStatus(401);
    }
}
