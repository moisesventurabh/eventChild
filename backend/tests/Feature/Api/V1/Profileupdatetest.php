<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_update_profile()
    {
        $response = $this->putJson(route('profile.update'), [
            'name' => 'Novo Nome',
            'email' => 'novo@exemplo.com',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_update_name_and_email()
    {
        $user = User::factory()->create([
            'name' => 'Nome Antigo',
            'email' => 'antigo@exemplo.com',
        ]);

        $response = $this->actingAs($user)
            ->putJson(route('profile.update'), [
                'name' => 'Nome Novo',
                'email' => 'novo@exemplo.com',
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Perfil atualizado com sucesso.')
                 ->assertJsonPath('user.name', 'Nome Novo')
                 ->assertJsonPath('user.email', 'novo@exemplo.com');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Novo',
            'email' => 'novo@exemplo.com',
        ]);
    }

    public function test_user_can_keep_their_own_email_without_uniqueness_error()
    {
        $user = User::factory()->create(['email' => 'meuemail@exemplo.com']);

        $response = $this->actingAs($user)
            ->putJson(route('profile.update'), [
                'name' => 'Nome Atualizado',
                'email' => 'meuemail@exemplo.com',
            ]);

        $response->assertStatus(200);
    }

    public function test_email_must_be_unique_among_other_users()
    {
        $user = User::factory()->create(['email' => 'meu@exemplo.com']);
        User::factory()->create(['email' => 'ocupado@exemplo.com']);

        $response = $this->actingAs($user)
            ->putJson(route('profile.update'), [
                'name' => $user->name,
                'email' => 'ocupado@exemplo.com',
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_update_fails_when_required_fields_are_missing()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson(route('profile.update'), []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_user_can_change_password_with_correct_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('senha-atual'),
        ]);

        $response = $this->actingAs($user)
            ->putJson(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'senha-atual',
                'password' => 'senha-nova-123',
                'password_confirmation' => 'senha-nova-123',
            ]);

        $response->assertStatus(200);

        $this->assertTrue(
            \Hash::check('senha-nova-123', $user->fresh()->password)
        );
    }

    public function test_password_update_fails_when_current_password_is_incorrect()
    {
        $user = User::factory()->create([
            'password' => bcrypt('senha-atual'),
        ]);

        $response = $this->actingAs($user)
            ->putJson(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'senha-errada',
                'password' => 'senha-nova-123',
                'password_confirmation' => 'senha-nova-123',
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['current_password']);

        // Garante que a senha antiga não foi alterada
        $this->assertTrue(
            \Hash::check('senha-atual', $user->fresh()->password)
        );
    }

    public function test_password_update_fails_when_current_password_is_not_provided()
    {
        $user = User::factory()->create([
            'password' => bcrypt('senha-atual'),
        ]);

        $response = $this->actingAs($user)
            ->putJson(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'senha-nova-123',
                'password_confirmation' => 'senha-nova-123',
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['current_password']);
    }

    public function test_password_update_fails_when_confirmation_does_not_match()
    {
        $user = User::factory()->create([
            'password' => bcrypt('senha-atual'),
        ]);

        $response = $this->actingAs($user)
            ->putJson(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'senha-atual',
                'password' => 'senha-nova-123',
                'password_confirmation' => 'nao-confere',
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    public function test_profile_update_without_password_fields_does_not_change_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('senha-original'),
        ]);

        $response = $this->actingAs($user)
            ->putJson(route('profile.update'), [
                'name' => 'Novo Nome',
                'email' => $user->email,
            ]);

        $response->assertStatus(200);

        $this->assertTrue(
            \Hash::check('senha-original', $user->fresh()->password)
        );
    }
}