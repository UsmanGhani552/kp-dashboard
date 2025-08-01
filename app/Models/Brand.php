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
        'logo_mini',
    ];

    protected $appends = ['logo_url', 'logo_mini_url'];
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('images/brands/' . $this->logo);
        }
        return asset('images/clients/default.png');
    }
    public function getLogoMiniUrlAttribute()
    {
        if ($this->logo_mini) {
            return asset('images/brands/' . $this->logo_mini);
        }
        return asset('images/clients/default.png');
    }
    public static function createBrand(array $data)
    {
        $data['logo'] = (new self)->uploadImage(request(), 'logo', 'images/brands');
        $data['logo_mini'] = (new self)->uploadImage(request(), 'logo_mini', 'images/brands');
        return self::create($data);
    }
    public function updateBrand(array $data)
    {
        $data['logo'] = $this->uploadImage(request(), 'logo', 'images/brands', "images/brands/{$this->logo}", $this->logo);
        $data['logo_mini'] = $this->uploadImage(request(), 'logo_mini', 'images/brands', "images/brands/{$this->logo_mini}", $this->logo_mini);
        return $this->update($data);
    }
    public function deleteBrand()
    {
        $this->deleteImage("images/brands/{$this->logo}");
        $this->deleteImage("images/brands/{$this->logo_mini}");
        $this->delete();
    }
}
