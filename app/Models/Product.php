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

    /**
     * Root-relative URL path (/images/products/…) for uploaded product files.
     * Unlike asset(), this does not use APP_URL, so images still load when the env URL
     * does not match the browser host (common on Render if APP_URL is wrong).
     */
    public static function normalizeStoredImageHref(?string $image): ?string
    {
        if (!$image || $image === 'example.image') {
            return null;
        }
        $path = trim(str_replace('\\', '/', $image));
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return '/' . ltrim($path, '/');
    }

    public function storedImageHref(): ?string
    {
        return self::normalizeStoredImageHref($this->image);
    }
}
