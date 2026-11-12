<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class AuthLog extends Model
{
    protected $fillable = [
        'user_id',
        'email_attempted',
        'method',
        'status',
        'duration_ms',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(Request $request, string $method, string $status, ?User $user = null, ?int $durationMs = null): self
    {
        return self::create([
            'user_id' => $user?->id,
            'email_attempted' => $user?->email ?? $request->input('email'),
            'method' => $method,
            'status' => $status,
            'duration_ms' => $durationMs,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);
    }
}
