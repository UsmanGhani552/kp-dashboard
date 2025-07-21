<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Client extends Authenticatable
{
    use HasApiTokens, Notifiable, ImageUploadTrait, HasRoles;
    protected $table = 'users';
    protected $guard_name = 'client';

    protected $fillable = [
        'name',
        'email',
        'username',
        'address',
        'phone',
        'image',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'updated_at',
    ];
    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('images/clients/' . $this->image);
        }
        return asset('images/clients/default.png');
    }
    public static function createClient(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['image'] = (new self)->uploadImage(request(), 'image', 'images/clients');
        $client = self::create($data);
        $client->assignRole('client');

        return $client;
    }

    public function updateClient(array $data, $package = null)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $data['image'] = $this->uploadImage(request(), 'image', 'images/clients', "images/clients/{$this->image}", $this->image);
        $this->update([
            'name' => $data['name'] ?? $this->name,
            'phone' => $data['phone'] ?? $this->phone,
            'email' => $data['email'] ?? $this->email,
            'password' => $data['password'] ?? $this->password,
            'image' => $data['image'],
        ]);
        if (!empty($data['package_id'])) {
             $this->assignPackageWithInvoice($this, $package);
        }
        return $this->fresh();
    }
    public static function assignPackageWithInvoice(Client $client, array $package)
    {
        // Assign the package
        ClientAssignedPackage::assignPackage([
            'client_id' => $client->id,
            'package_id' => $package['id'],
        ]);

        // Create the invoice
        Invoice::createInvoice([
            'client_id' => $client->id,
            'title' => $package['name'],
            'price' => $package['price'],
            'remaining_price' => 0,
            'description' => $package['description'],
            'category_id' => $package['category_id'],
            'payment_type_id' => 3,
            'sale_type' => 'fresh sale'
        ]);
        return $client->fresh();
    }

    public function deleteClient()
    {
        $this->deleteImage("images/clients/{$this->image}");
        $this->delete();
    }

    public function editProfile(array $data): void
    {
        $data['image'] = $this->uploadImage(request(), 'image', 'images/clients', "images/clients/{$this->image}", $this->image);
        $data['password'] = isset($data['password']) ? Hash::make($data['password']) : $this->password;
        $this->update($data);
        if (!empty($data['emails'])) {
            $this->clientEmails()->delete();
            ClientEmail::updateClientEmails($data, $this->id);
        }
    }

    public function clientEmails()
    {
        return $this->hasMany(ClientEmail::class);
    }

    public function packages()
    {
        return $this->belongsToMany(
            Package::class,
            'client_assigned_packages',
            'client_id',
            'package_id'
        );
    }
}
