<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageDeliverables extends Model
{
    protected $fillable = [
        'package_id',
        'name',
    ];

    public static function createPackageDeliverables($package, $deliverables): void
    {
        foreach ($deliverables as $deliverable) {
            self::createDeliverable($package, $deliverable);
        }
    }
    public static function createDeliverable($package, $deliverable): void
    {
        self::create([
            'package_id' => $package->id,
            'name' => $deliverable['name']
        ]);
    }

    /**
     * Get the package that owns the deliverables.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
