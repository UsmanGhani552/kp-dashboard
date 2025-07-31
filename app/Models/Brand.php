<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use ImageUploadTrait;
    protected $fillable = [
        'name',
        'email',
        'address',
        'logo',
    ];

    protected $appends = ['logo_url'];
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('images/brands/' . $this->logo);
        }
        return asset('images/clients/default.png');
    }
    public static function createBrand(array $data)
    {
        $data['logo'] = (new self)->uploadImage(request(), 'logo', 'images/brands');
        return self::create($data);
    }
    public function updateBrand(array $data)
    {
        $data['logo'] = $this->uploadImage(request(), 'logo', 'images/brands');
        return $this->update($data);
    }
    public function deleteBrand()
    {
        $this->deleteImage("images/brands/{$this->logo}");
        $this->delete();
    }
}
