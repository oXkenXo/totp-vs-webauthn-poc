<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laragear\WebAuthn\Contracts\WebAuthnAuthenticatable;
use Laragear\WebAuthn\WebAuthnAuthentication;

#[Fillable(['name', 'email', 'password', 'nim', 'prodi', 'kelas', 'totp_secret', 'totp_enabled'])]
#[Hidden(['password', 'remember_token', 'totp_secret'])]
class User extends Authenticatable implements WebAuthnAuthenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, WebAuthnAuthentication;

    public function authLogs(): HasMany
    {
        return $this->hasMany(AuthLog::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'totp_enabled' => 'boolean',
        ];
    }
}
