<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'orderItems';

    protected $primaryKey = 'orderItemID';

    protected $fillable = [
        'orderID', 'productID', 'quantity', 'unitPrice',
    ];

    protected $casts = [
        'unitPrice' => 'decimal:2',
    ];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function lineTotal(): float
    {
        return $this->unitPrice * $this->quantity;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'orderID', 'orderID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID', 'productID');
    }
}
