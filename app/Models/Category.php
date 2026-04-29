<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'categoryID';

    protected $fillable = [
        'catName',
    ];

    public function getCNameAttribute(): string
    {
        return $this->catName ?? '';
    }

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function products()
    {
        return $this->hasMany(Product::class, 'categoryID', 'categoryID');
    }
}
