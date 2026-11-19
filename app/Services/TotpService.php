<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class TotpService
{
    public function __construct(private readonly Google2FA $google2fa) {}

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function qrCodeSvg(User $user, string $secret): string
    {
        $otpauthUri = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret,
        );

        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd);

        return (new Writer($renderer))->writeString($otpauthUri);
    }

    /**
     * Returns the matched time-step, or null when the code is wrong or its time-step was already used.
     */
    public function verifyNewer(string $secret, string $code, ?int $lastUsedTimestep): ?int
    {
        // google2fa only returns the time-step (instead of true) when given a previous one, so 0 stands in for "never".
        $timestep = $this->google2fa->verifyKeyNewer($secret, $code, $lastUsedTimestep ?? 0);

        return $timestep === false ? null : (int) $timestep;
    }
}
