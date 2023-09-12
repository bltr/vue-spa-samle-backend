<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_register()
    {
        $email = 'admin@mail.com';
        $response = $this->register(['email' => $email]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token', $response->json());
        $this->assertIsString($response->json()['access_token']);
        $this->assertAuthenticated();
        $user = User::whereEmail($email)->first();
        $this->assertNotEmpty($user);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    /**
     * @test
     */
    public function email_is_required()
    {
        $response = $this->register(['email' => null]);

        $response->assertStatus(422);
        $this->assertError($response, 'email', 'The email field is required.');
    }

    /**
     * @test
     */
    public function email_must_be_valid()
    {
        $response = $this->register(['email' => 'invalid email']);

        $response->assertStatus(422);
        $this->assertError($response, 'email', 'The email must be a valid email address.');
    }

    /**
     * @test
     */
    public function can_not_register_with_already_taken_email()
    {
        $email = 'already-taken-email@mail.com';
        User::factory()->create(['email' => $email]);

        $response = $this->register(['email' => $email]);

        $response->assertStatus(422);
        $this->assertError($response, 'email', 'The email has already been taken.');
    }

    /**
     * @test
     */
    public function password_is_required()
    {
        $response = $this->register(['password' => null]);

        $response->assertStatus(422);
        $this->assertError($response, 'password', 'The password field is required.');
    }

    /**
     * @test
     */
    public function password_must_be_more_than_eight()
    {
        $response = $this->register(['password' => '1234567']);

        $response->assertStatus(422);
        $response->assertJson(
            [
                "message" => "The password must be at least 8 characters. (and 1 more error)",
                "errors" => [
                    "password" => [
                        "The password must be at least 8 characters.",
                        "The password confirmation does not match."
                    ]
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function password_must_be_confirmed()
    {
        $response = $this->register([
            'password' => 'password',
            'password_confirmation' => 'passwor'
        ]);

        $response->assertStatus(422);
        $this->assertError($response, 'password', 'The password confirmation does not match.');
    }

    private function register($attributes)
    {
        return $this->postJson('/api/auth/register', array_merge([
            'email' => 'admin@mail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ], $attributes));
    }

    private function assertError(TestResponse $response, $attribute, $message)
    {
        $response->assertJson([
            'message' => $message,
            'errors' => [
                $attribute => [$message]
            ]
        ]);
    }
}
