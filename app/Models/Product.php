<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
     * Root-relative URL for product images.
     * New uploads live on the public disk (/storage/…). Older rows may point at files under public/.
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

        $relative = ltrim($path, '/');

        if (Storage::disk('public')->exists($relative)) {
            return '/storage/'.$relative;
        }

        if (is_file(public_path($relative))) {
            return '/'.$relative;
        }

        if (str_starts_with($relative, 'images/products/')) {
            return '/storage/'.$relative;
        }

        return '/'.$relative;
    }

    public function storedImageHref(): ?string
    {
        return self::normalizeStoredImageHref($this->image);
    }
}
