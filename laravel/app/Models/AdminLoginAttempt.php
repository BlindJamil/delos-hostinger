<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLoginAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ip',
        'email',
        'success',
        'user_agent',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'attempted_at' => 'datetime',
        ];
    }

    /**
     * Count failed attempts from this IP in the last N minutes.
     */
    public static function recentFailuresForIp(string $ip, int $minutes = 15): int
    {
        return static::where('ip', $ip)
            ->where('success', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Is this IP currently locked out? (5+ failures in 15 minutes)
     */
    public static function isLockedOut(string $ip): bool
    {
        return static::recentFailuresForIp($ip, 15) >= 5;
    }
}
