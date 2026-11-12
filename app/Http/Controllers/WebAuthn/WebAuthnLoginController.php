<?php

namespace App\Http\Controllers\WebAuthn;

use App\Models\AuthLog;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;

use function response;

class WebAuthnLoginController
{
    /**
     * Returns the challenge to assertion.
     */
    public function options(AssertionRequest $request): Responsable
    {
        return $request->toVerify($request->validate(['email' => 'sometimes|email|string']));
    }

    /**
     * Log the user in.
     */
    public function login(AssertedRequest $request): Response
    {
        $start = Session::get('auth_flow_started_at');
        $user = $request->login();

        $durationMs = $start ? (int) round((microtime(true) - $start) * 1000) : null;

        AuthLog::record($request, 'webauthn', $user ? 'success' : 'failed', $user, $durationMs);

        return response()->noContent($user ? 204 : 422);
    }
}
