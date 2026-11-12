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

        if (! $secret || ! $totp->verify($secret, $request->string('code')->toString())) {
            return back()->withErrors(['code' => 'Kode TOTP salah. Pastikan waktu di HP kamu sinkron, lalu coba lagi.']);
        }

        $request->user()->update([
            'totp_secret' => $secret,
            'totp_enabled' => true,
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
        ]);

        return redirect()->route('account.security')->with('status', 'TOTP dinonaktifkan.');
    }
}
