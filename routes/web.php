<?php

use App\Http\Controllers\Account\PasskeyController;
use App\Http\Controllers\Account\SecurityController;
use App\Http\Controllers\Account\TotpController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebAuthn\WebAuthnLoginController;
use App\Http\Controllers\WebAuthn\WebAuthnRegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('login/totp', [LoginController::class, 'showTotp'])->name('login.totp');
    Route::post('login/totp', [LoginController::class, 'verifyTotp'])->name('login.totp.verify');

    // Passwordless WebAuthn/passkey login challenge + verification (guest-accessible by design).
    Route::post('webauthn/login/options', [WebAuthnLoginController::class, 'options'])->name('webauthn.login.options');
    Route::post('webauthn/login', [WebAuthnLoginController::class, 'login'])->name('webauthn.login');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'show'])->name('dashboard');

    Route::get('account/security', [SecurityController::class, 'show'])->name('account.security');

    Route::get('account/totp/setup', [TotpController::class, 'setup'])->name('account.totp.setup');
    Route::post('account/totp/enable', [TotpController::class, 'enable'])->name('account.totp.enable');
    Route::delete('account/totp', [TotpController::class, 'disable'])->name('account.totp.disable');

    Route::delete('account/passkeys/{credentialId}', [PasskeyController::class, 'destroy'])->name('account.passkeys.destroy');

    // Passkey enrollment requires an authenticated user (the package's AttestationRequest::authorize() enforces this).
    Route::post('webauthn/register/options', [WebAuthnRegisterController::class, 'options'])->name('webauthn.register.options');
    Route::post('webauthn/register', [WebAuthnRegisterController::class, 'register'])->name('webauthn.register');
});
