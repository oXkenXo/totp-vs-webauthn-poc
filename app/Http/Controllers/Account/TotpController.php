<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class TotpController extends Controller
{
    public function setup(TotpService $totp): View|RedirectResponse
    {
        $user = request()->user();

        if ($user->totp_enabled) {
            return redirect()->route('account.security');
        }

        $secret = Session::get('totp_setup_secret') ?? $totp->generateSecret();
        Session::put('totp_setup_secret', $secret);

        return view('account.totp', [
            'secret' => $secret,
            'qr' => $totp->qrCodeSvg($user, $secret),
        ]);
    }

    public function enable(Request $request, TotpService $totp): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $secret = Session::get('totp_setup_secret');

        $timestep = $secret ? $totp->verifyNewer($secret, $request->string('code')->toString(), null) : null;

        if ($timestep === null) {
            return back()->withErrors(['code' => 'Kode TOTP salah. Pastikan waktu di HP kamu sinkron, lalu coba lagi.']);
        }

        // Recording the setup code's time-step stops it from also being accepted at the next login.
        $request->user()->update([
            'totp_secret' => $secret,
            'totp_enabled' => true,
            'totp_last_timestep' => $timestep,
        ]);

        Session::forget('totp_setup_secret');

        return redirect()->route('account.security')->with('status', 'TOTP berhasil diaktifkan.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $request->user()->update([
            'totp_secret' => null,
            'totp_enabled' => false,
            'totp_last_timestep' => null,
        ]);

        return redirect()->route('account.security')->with('status', 'TOTP dinonaktifkan.');
    }
}
