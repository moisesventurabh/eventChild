<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationAndPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register()
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Teste Usuário',
            'email' => 'teste@exemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Conta criada com sucesso.');
        
        $this->assertDatabaseHas('users', ['email' => 'teste@exemplo.com']);
    }

    public function test_registration_fails_when_required_fields_are_missing()
    {
        $response = $this->postJson(route('register'), []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);

        $this->assertGuest();
    }

    public function test_registration_fails_when_email_is_already_taken()
    {
        User::factory()->create(['email' => 'existente@exemplo.com']);

        $response = $this->postJson(route('register'), [
            'name' => 'Outro Usuário',
            'email' => 'existente@exemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);

        $this->assertGuest();
    }

    public function test_registration_fails_when_password_confirmation_does_not_match()
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Teste Usuário',
            'email' => 'teste@exemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password-diferente',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'teste@exemplo.com']);
    }

    public function test_user_can_request_password_reset_link()
    {
        $user = User::factory()->create(['email' => 'teste@exemplo.com']);

        $response = $this->postJson(route('password.email'), [
            'email' => 'teste@exemplo.com',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Se este e-mail estiver cadastrado, um link de recuperação foi enviado.');
    }

    public function test_password_reset_request_returns_same_message_for_unknown_email()
    {
        $response = $this->postJson(route('password.email'), [
            'email' => 'nao-cadastrado@exemplo.com',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Se este e-mail estiver cadastrado, um link de recuperação foi enviado.');
    }

    public function test_password_reset_link_dispatches_the_custom_notification_with_correct_url()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'teste@exemplo.com']);

        $this->postJson(route('password.email'), [
            'email' => 'teste@exemplo.com',
        ]);

        Notification::assertSentTo(
            $user,
            function (ResetPasswordNotification $notification) use ($user) {
                $mail = $notification->toMail($user);
                $actionUrl = $mail->actionUrl;

                return str_contains($actionUrl, config('app.frontend_url', config('app.url')))
                    && str_contains($actionUrl, 'token=')
                    && str_contains($actionUrl, urlencode($user->email));
            }
        );
    }


    public function test_user_can_reset_password_with_a_valid_token()
    {
        $user = User::factory()->create(['email' => 'teste@exemplo.com']);
        $token = Password::createToken($user);

        $response = $this->postJson(route('password.update'), [
            'token' => $token,
            'email' => 'teste@exemplo.com',
            'password' => 'nova-senha-123',
            'password_confirmation' => 'nova-senha-123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Senha redefinida com sucesso.');

        $this->assertTrue(
            \Hash::check('nova-senha-123', $user->fresh()->password)
        );
    }

    public function test_password_reset_fails_with_an_invalid_token()
    {
        $user = User::factory()->create(['email' => 'teste@exemplo.com']);
        $originalPassword = $user->password;

        $response = $this->postJson(route('password.update'), [
            'token' => 'token-invalido',
            'email' => 'teste@exemplo.com',
            'password' => 'nova-senha-123',
            'password_confirmation' => 'nova-senha-123',
        ]);

        $response->assertStatus(422);

        $this->assertEquals($originalPassword, $user->fresh()->password);
    }

    public function test_password_reset_fails_when_confirmation_does_not_match()
    {
        $user = User::factory()->create(['email' => 'teste@exemplo.com']);
        $token = Password::createToken($user);

        $response = $this->postJson(route('password.update'), [
            'token' => $token,
            'email' => 'teste@exemplo.com',
            'password' => 'nova-senha-123',
            'password_confirmation' => 'outra-senha',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }
}