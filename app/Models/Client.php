<?php

namespace App\Models;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class Client extends Model
{
    use ImageUploadTrait, HasRoles;
    protected $table = 'users';
    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'username',
        'phone',
        'image',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    public static function createClient(array $data): void
    {
        $data['password'] = Hash::make($data['password']);
        $data['image'] = (new self)->uploadImage(request(), 'image', 'images/clients');
        $client = self::create($data);
        $client->assignRole('client');
    }

    public function updateClient(array $data, $package = null): void
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
            ClientAssignedPackage::assignPackage([
                'client_id' => $this->id,
                'package_id' => $data['package_id'],
            ]);
            // dd('asd');
            Invoice::createInvoice([
                'client_id' => $this->id,
                'title' => $package['name'],
                'price' => $package['price'],
                'remaining_price' => 0,
                'description' => $package['description'],
                'category_id' => $package['category_id'],
                'payment_type_id' => 3,
                'sale_type' => 'fresh sale'
            ]);
        }
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
        if(!empty($data['emails'])){
            $this->clientEmails()->delete();
            ClientEmail::updateClientEmails($data,$this->id);
        }
    }

    public function clientEmails() {
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
