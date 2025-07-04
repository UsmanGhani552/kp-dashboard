<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'type',
        'status',
    ];

    public static function saveLoginActivity($user_id,$type): void
    {
        self::create([
            'user_id' => $user_id,
            'ip_address' => request()->ip(),
            'type' => $type,
            'status' => 1,
        ]);
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
