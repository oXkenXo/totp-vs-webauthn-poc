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

    public function verify(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code) !== false;
    }
}
