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
    protected $guard_name = 'web';

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


    public static function changePassword(self $user, array $data): void
    {
        $data['password'] = Hash::make($data['password']);
        $user->update(['password' => $data['password']]);
    }

    public static function register(array $data)
    {
        $user = self::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
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
            'address' => $data['address'] ?? $this->phone,
            'email' => $data['email'] ?? $this->email,
            'password' => $data['password'] ?? $this->password,
            'image' => $data['image'],
        ]);
        if (!empty($data['package_id'])) {
            $this->assignPackageWithInvoice($package, true);
        }
        return $this->fresh();
    }
    public function assignPackageWithInvoice(array $package, $returnClient = false)
    {
        // Assign the package
        $assignedPackage = ClientAssignedPackage::assignPackage([
            'client_id' => $this->id,
            'package_id' => $package['id'],
        ]);

        // Create the invoice
        Invoice::createInvoice([
            'user_id' => auth()->user()->id,
            'client_id' => $this->id,
            'package_id' => $package['id'],
            'assigned_package_id' => $assignedPackage->id,
            'title' => $package['name'],
            'price' => $package['price'],
            'remaining_price' => 0,
            'description' => $package['description'],
            'category_id' => $package['category_id'],
            'payment_type_id' => 3,
            'brand_id' => 1,
            'sale_type' => 'Fresh Sale'
        ]);
        return $returnClient ? $this->fresh() : $assignedPackage->load('package.category', 'package.deliverables');
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
    public function resetToken()
    {
        return $this->hasOne(PasswordResetToken::class,'user_id');
    }
}
