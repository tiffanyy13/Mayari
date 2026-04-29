<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'productID';

    protected $fillable = [
        'categoryID', 'pName', 'descript',
        'variants', 'price', 'stock', 'image', 'isArchived',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'variants'   => 'array',
        'isArchived' => 'boolean',
    ];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function scopeActive($query)
    {
        return $query->where('isArchived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('isArchived', true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryID', 'categoryID');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'productID', 'productID');
    }
}
