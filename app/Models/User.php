<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'first_name',
    'second_name',
    'email',
    'phone_number',
    'address',
    'password',
    'avatar',
    'role',
    'is_active',
])]


#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

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
    /**
     * Get the user's wishlist.
     */
    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }

    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->second_name}";
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
