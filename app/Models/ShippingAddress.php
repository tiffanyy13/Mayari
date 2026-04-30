<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $table = 'shippingaddresses';
    protected $primaryKey = 'shippingAddressID';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'userID',
        'fullName',
        'phone',
        'addressLine',
        'city',
        'province',
        'country',
        'postal',
        'landmark',
        'label',
        'isDefault',
    ];

    protected $casts = [
        'isDefault' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }
}

