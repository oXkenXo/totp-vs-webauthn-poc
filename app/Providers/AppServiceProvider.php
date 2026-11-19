<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        $tooMany = fn (array $headers): string => 'Terlalu banyak percobaan. Coba lagi dalam '.($headers['Retry-After'] ?? 60).' detik.';

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)
            ->by(Str::lower($request->string('email')->toString()).'|'.$request->ip())
            ->response(fn (Request $request, array $headers) => back()
                ->withErrors(['email' => $tooMany($headers)])
                ->onlyInput('email')));

        // Keyed by the pending user, not the IP, so rotating IPs does not buy more guesses at the 6-digit code.
        RateLimiter::for('totp', fn (Request $request) => Limit::perMinute(5)
            ->by('totp|'.$request->session()->get('totp_pending_user_id', $request->ip()))
            ->response(fn (Request $request, array $headers) => back()
                ->withErrors(['code' => $tooMany($headers)])));

        // Passkey assertions cannot be guessed, so this only curbs abuse; it stays looser because
        // research participants on one campus network share a single public IP. Each route gets its
        // own bucket so fetching options does not eat into the login attempts.
        RateLimiter::for('passkey-login', fn (Request $request) => Limit::perMinute(10)
            ->by('passkey|'.$request->route()?->getName().'|'.$request->ip())
            ->response(fn (Request $request, array $headers) => response()
                ->json(['message' => $tooMany($headers)], 429, $headers)));
    }
}
