<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TotpLoginTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'JBSWY3DPEHPK3PXP';

    public function test_valid_totp_code_completes_login(): void
    {
        $user = $this->userWithTotp();

        $this->passPasswordStep($user);
        $response = $this->post(route('login.totp.verify'), ['code' => $this->currentCode()]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->totp_last_timestep);
    }

    public function test_same_totp_code_cannot_be_used_twice(): void
    {
        $user = $this->userWithTotp();
        $code = $this->currentCode();

        $this->passPasswordStep($user);
        $this->post(route('login.totp.verify'), ['code' => $code])->assertRedirect(route('dashboard'));
        $this->post(route('logout'));

        $this->passPasswordStep($user);
        $response = $this->post(route('login.totp.verify'), ['code' => $code]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
        $this->assertDatabaseHas('auth_logs', ['user_id' => $user->id, 'method' => 'totp', 'status' => 'failed']);
    }

    public function test_enabling_totp_persists_secret_and_blocks_reusing_the_setup_code(): void
    {
        $user = User::factory()->create(['password' => 'secret-password-123']);
        $code = $this->currentCode();

        $this->actingAs($user)
            ->withSession(['totp_setup_secret' => self::SECRET])
            ->post(route('account.totp.enable'), ['code' => $code])
            ->assertRedirect(route('account.security'));

        $user->refresh();
        $this->assertTrue($user->totp_enabled);
        $this->assertSame(self::SECRET, $user->totp_secret);
        $this->assertNotNull($user->totp_last_timestep);

        $this->post(route('logout'));
        $this->passPasswordStep($user);
        $this->post(route('login.totp.verify'), ['code' => $code])->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_totp_secret_is_encrypted_at_rest(): void
    {
        $user = $this->userWithTotp();

        $stored = DB::table('users')->where('id', $user->id)->value('totp_secret');

        $this->assertNotSame(self::SECRET, $stored);
        $this->assertSame(self::SECRET, Crypt::decryptString($stored));
        $this->assertSame(self::SECRET, $user->fresh()->totp_secret);
    }

    public function test_totp_guessing_is_rate_limited(): void
    {
        $this->freezeTime();
        $user = $this->userWithTotp();
        $this->passPasswordStep($user);

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login.totp.verify'), ['code' => '000000']);
        }

        $response = $this->post(route('login.totp.verify'), ['code' => $this->currentCode()]);

        $response->assertSessionHasErrors(['code' => 'Terlalu banyak percobaan. Coba lagi dalam 60 detik.']);
        $this->assertGuest();
    }

    private function userWithTotp(): User
    {
        return User::factory()->create([
            'password' => 'secret-password-123',
            'totp_secret' => self::SECRET,
            'totp_enabled' => true,
        ]);
    }

    private function passPasswordStep(User $user): void
    {
        $this->post(route('login'), ['email' => $user->email, 'password' => 'secret-password-123'])
            ->assertRedirect(route('login.totp'));
    }

    private function currentCode(): string
    {
        return app(Google2FA::class)->getCurrentOtp(self::SECRET);
    }
}
