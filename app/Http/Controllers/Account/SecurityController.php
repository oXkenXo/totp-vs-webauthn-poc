<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('account.security', [
            'passkeys' => $user->webAuthnCredentials()->latest()->get(),
            'authLogs' => $user->authLogs()->latest()->limit(15)->get(),
        ]);
    }
}
