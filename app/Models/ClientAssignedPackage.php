<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAssignedPackage extends Model
{
    protected $fillable = [
        'client_id',
        'package_id',
        'status',
    ];

    public static function assignPackage(array $data): self
    {
        return self::updateOrCreate(
            [
                'client_id' => $data['client_id'],
                'package_id' => $data['package_id']
            ]
        );
    }

    /**
     * Get the user that owns the assigned package.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * Get the package that is assigned to the user.
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function invoice() {
        return $this->hasOne(Invoice::class,'assigned_package_id');
    }
}
