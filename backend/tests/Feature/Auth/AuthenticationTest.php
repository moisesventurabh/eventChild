<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_endpoint()
    {
        $user = User::factory()->create([
            'email' => 'moises@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'moises@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Autenticado com sucesso.');

        $this->assertAuthenticatedAs($user);
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'moises@example.com',
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'moises@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertGuest();
    }

    public function test_users_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('logout'));

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Sessão encerrada com sucesso.');

        $this->assertGuest();
    }
}