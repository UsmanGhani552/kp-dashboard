<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    /**
     * Get the packages associated with the category.
     */
    public function packages()
    {
        return $this->hasMany(Package::class, 'category');
    }
}
