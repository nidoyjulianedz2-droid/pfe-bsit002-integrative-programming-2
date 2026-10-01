<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receives_token(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
        ])
            ->assertCreated()
            ->assertJsonStructure(['message', 'token', 'user' => ['id', 'name', 'email']])
            ->assertJsonMissingPath('user.password');
    }

    public function test_register_validates_input(): void
    {
        $this->postJson('/api/register', ['email' => 'not-an-email', 'password' => '123'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'password123']);

        $this->postJson('/api/login', ['email' => 'admin@example.com', 'password' => 'password123'])
            ->assertOk()
            ->assertJsonPath('message', 'Login successful')
            ->assertJsonStructure(['token']);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'password123']);

        $this->postJson('/api/login', ['email' => 'admin@example.com', 'password' => 'wrong-password'])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Invalid login credentials');
    }

    public function test_protected_route_rejects_request_without_token(): void
    {
        // Plain get() without an Accept header, just like a fresh Postman tab.
        $this->get('/api/employees')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_token_gives_access_and_logout_revokes_it(): void
    {
        $token = $this->postJson('/api/register', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
        ])->json('token');

        $headers = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/employees', $headers)->assertOk();

        $this->postJson('/api/logout', [], $headers)
            ->assertOk()
            ->assertJsonPath('message', 'Logged out successfully');

        $this->assertDatabaseCount('personal_access_tokens', 0);

        // Forget the user cached by the auth guard so the next request is re-authenticated.
        $this->app['auth']->forgetGuards();

        $this->getJson('/api/employees', $headers)->assertUnauthorized();
    }
}
