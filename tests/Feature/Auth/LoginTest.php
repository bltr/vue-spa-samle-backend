<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_login()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'password']);

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token', $response->json());
        $this->assertIsString($response->json()['access_token']);
        $this->assertAuthenticated();
    }

    /**
     * @test
     */
    public function user_can_not_login_with_not_registered_email()
    {
        User::factory()->create(['email' => 'admin@mail.com']);

        $response = $this->postJson(
            '/api/auth/login',
            ['email' => 'not_registered@mail.com', 'password' => 'password']
        );

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized']);
    }


    /**
     * @test
     */
    public function user_can_not_login_with_incorrect_password()
    {
        $user = User::factory()->create();

        $response = $this->postJson(
            '/api/auth/login',
            ['email' => $user->email, 'password' => 'incorrect_password']
        );

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized']);
    }

    /**
     * @test
     */
    public function email_is_required()
    {
        $response = $this->postJson('/api/auth/login', ['password' => 'password']);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The email field is required.',
            'errors' => [
                'email' => ['The email field is required.']
            ]
        ]);
    }

    /**
     * @test
     */
    public function email_must_be_valid()
    {
        $response = $this->postJson('/api/auth/login', ['email' => 'invalid-email', 'password' => 'password']);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The email must be a valid email address.',
            'errors' => [
                'email' => ['The email must be a valid email address.']
            ]
        ]);
    }

    /**
     * @test
     */
    public function password_is_required()
    {
        $response = $this->postJson('/api/auth/login', ['email' => 'admin@mail.com']);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The password field is required.',
            'errors' => [
                'password' => ['The password field is required.']
            ]
        ]);
    }
}
