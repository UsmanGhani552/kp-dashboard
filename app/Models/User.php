<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, ImageUploadTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'phone',
        'image',
        'social_id',
        'provider',
        'password',
    ];
    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('images/users/' . $this->image);
        }
        return asset('images/users/default.png');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['image'] = (new self)->uploadImage(request(), 'image', 'images/users');

        $user = self::create($data);
        $user->assignRole('admin');
        return $user;
    }

    public function updateUser(array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $data['image'] = $this->uploadImage(request(), 'image', 'images/users', "images/users/{$this->image}", $this->image);
        $this->update($data);
        return $this;
    }

    public function deleteUser()
    {
        $this->deleteImage("images/users/{$this->image}");
        $this->delete();
    }

    public function resetToken()
    {
        return $this->hasOne(PasswordResetToken::class);
    }
}
