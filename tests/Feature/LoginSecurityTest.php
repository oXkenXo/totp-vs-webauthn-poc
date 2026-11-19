<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_login_is_rate_limited_per_email(): void
    {
        $this->freezeTime();
        $user = User::factory()->create(['password' => 'secret-password-123']);

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $response = $this->post(route('login'), ['email' => $user->email, 'password' => 'secret-password-123']);

        $response->assertSessionHasErrors(['email' => 'Terlalu banyak percobaan. Coba lagi dalam 60 detik.']);
        $this->assertGuest();
    }

    public function test_passkey_login_is_rate_limited_with_a_json_message(): void
    {
        $this->freezeTime();

        foreach (range(1, 10) as $attempt) {
            $this->postJson(route('webauthn.login'), []);
        }

        $this->postJson(route('webauthn.login'), [])
            ->assertStatus(429)
            ->assertJson(['message' => 'Terlalu banyak percobaan. Coba lagi dalam 60 detik.']);
    }

    public function test_passkey_options_are_rate_limited_separately_from_passkey_login(): void
    {
        foreach (range(1, 10) as $attempt) {
            $this->postJson(route('webauthn.login.options'))->assertOk();
        }

        $this->postJson(route('webauthn.login.options'))->assertStatus(429);
        $this->postJson(route('webauthn.login'), [])->assertStatus(422);
    }

    public function test_totp_last_timestep_cannot_be_mass_assigned(): void
    {
        $user = User::factory()->create();

        $user->fill(['totp_last_timestep' => 999])->save();

        $this->assertNull($user->fresh()->totp_last_timestep);
    }

    public function test_passkey_options_require_user_verification(): void
    {
        $this->postJson(route('webauthn.login.options'))
            ->assertOk()
            ->assertJsonPath('userVerification', 'required');

        $this->actingAs(User::factory()->create())
            ->postJson(route('webauthn.register.options'))
            ->assertOk()
            ->assertJsonPath('authenticatorSelection.userVerification', 'required');
    }

    public function test_login_and_register_pages_warn_this_is_not_the_official_igracias(): void
    {
        $this->get(route('login'))->assertSee('Prototipe riset SecuLab, bukan iGracias resmi.');
        $this->get(route('register'))->assertSee('Prototipe riset SecuLab, bukan iGracias resmi.');
    }
}
