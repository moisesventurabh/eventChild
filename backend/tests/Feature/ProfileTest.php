<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route('profile.update'), [
            'name' => 'Novo Nome',
            'email' => 'novo@email.com',
        ]);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals('Novo Nome', $user->name);
    }

    public function test_password_can_be_updated_with_correct_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password123',
            'password' => 'nova-senha123',
            'password_confirmation' => 'nova-senha123',
        ]);

        $response->assertStatus(200);
        $this->assertTrue(Hash::check('nova-senha123', $user->fresh()->password));
    }

    public function test_password_cannot_be_updated_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'senha-errada',
            'password' => 'nova-senha123',
            'password_confirmation' => 'nova-senha123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['current_password']);
    }
}