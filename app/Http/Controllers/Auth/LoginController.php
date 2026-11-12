<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthLog;
use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        Session::put('auth_flow_started_at', microtime(true));

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Auth::guard()->getProvider()->validateCredentials($user, $credentials)) {
            AuthLog::record($request, 'password', 'failed', $user);

            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        if ($user->totp_enabled) {
            Session::put('totp_pending_user_id', $user->id);

            return redirect()->route('login.totp');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        AuthLog::record($request, 'password', 'success', $user, $this->elapsedMs());

        return redirect()->intended(route('dashboard'));
    }

    public function showTotp(): View|RedirectResponse
    {
        if (! Session::has('totp_pending_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.login-totp');
    }

    public function verifyTotp(Request $request, TotpService $totp): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $userId = Session::get('totp_pending_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $totp->verify($user->totp_secret, $request->string('code')->toString())) {
            AuthLog::record($request, 'totp', 'failed', $user);

            return back()->withErrors(['code' => 'Kode TOTP salah atau sudah kedaluwarsa.']);
        }

        Session::forget('totp_pending_user_id');
        Auth::login($user);
        $request->session()->regenerate();

        AuthLog::record($request, 'totp', 'success', $user, $this->elapsedMs());

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function elapsedMs(): ?int
    {
        $start = Session::get('auth_flow_started_at');

        return $start ? (int) round((microtime(true) - $start) * 1000) : null;
    }
}
