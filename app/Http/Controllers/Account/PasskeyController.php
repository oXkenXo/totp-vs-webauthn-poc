<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PasskeyController extends Controller
{
    public function destroy(Request $request, string $credentialId): RedirectResponse
    {
        $request->user()->webAuthnCredentials()->whereKey($credentialId)->delete();

        return redirect()->route('account.security')->with('status', 'Passkey dihapus.');
    }
}
