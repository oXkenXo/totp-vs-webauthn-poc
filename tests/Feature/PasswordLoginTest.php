<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_with_password_through_the_webauthn_user_provider(): void
    {
        $user = User::factory()->create(['password' => 'secret-password-123']);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'secret-password-123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('auth_logs', ['user_id' => $user->id, 'method' => 'password', 'status' => 'success']);
    }

    public function test_wrong_password_is_rejected_and_logged(): void
    {
        $user = User::factory()->create(['password' => 'secret-password-123']);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseHas('auth_logs', ['email_attempted' => $user->email, 'method' => 'password', 'status' => 'failed']);
    }

    public function test_user_with_totp_enabled_is_sent_to_the_totp_step(): void
    {
        $user = User::factory()->create([
            'password' => 'secret-password-123',
            'totp_secret' => 'JBSWY3DPEHPK3PXP',
            'totp_enabled' => true,
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'secret-password-123',
        ]);

        $response->assertRedirect(route('login.totp'));
        $this->assertGuest();
    }
}
