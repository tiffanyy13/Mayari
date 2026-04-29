<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'userID';

    protected $fillable = [
        'firstName',
        'lastName',
        'phone',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'rememberToken',
    ];

    protected $casts = [
        'emailVerifiedAt' => 'datetime',
    ];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getFullNameAttribute(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'userID', 'userID');
    }

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class, 'userID', 'userID')->orderByDesc('isDefault')->orderByDesc('createdAt');
    }

    public function defaultShippingAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'userID', 'userID')->where('isDefault', true);
    }
}
