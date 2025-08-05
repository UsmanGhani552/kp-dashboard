<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use ImageUploadTrait;
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'price',
        'additional_notes',
        'document',
    ];

    protected $appends = ['document_url'];
    public function getDocumentUrlAttribute()
    {
        if ($this->document) {
            return asset('images/packages/' . $this->document);
        }
        return asset('images/packages/default.png');
    }

    public static function createPackage(array $data) {
        $data['document'] = (new self)->uploadImage(request(),'document','images/packages');
        //  dd($data);
        $package = self::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'price' => $data['price'],
            'additional_notes' => $data['additional_notes'] ?? null,
            'document' => $data['document'] ?? null,    
        ]);
        return $package;
    }
    public function updatePackage(array $data) {
        $data['document'] = $this->uploadImage(request(),'document','images/packages', "images/packages/{$this->document}", $this->document);
       
        $this->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'price' => $data['price'],
            'additional_notes' => $data['additional_notes'],
            'document' => $data['document'],    
        ]);
        return $this->fresh();
    }
    public function deletePackage() {
        $this->deleteImage( "images/packages/{$this->document}");
        $this->delete();
    }

    /**
     * Get the category that owns the package.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function deliverables()
    {
        return $this->hasMany(PackageDeliverables::class);
    }
}
