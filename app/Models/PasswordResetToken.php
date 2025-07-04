<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
    ];
    public static function saveToken($user_id, $token): void
    {
        self::updateOrCreate(['user_id' => $user_id],[
            'token' => $token,
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
