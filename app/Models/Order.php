<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_PENDING   = 'Pending';
    const STATUS_ACCEPTED  = 'Accepted';
    const STATUS_SHIPPED   = 'Shipped';
    const STATUS_DELIVERED = 'Delivered';
    const STATUS_CANCELED  = 'Canceled';

    protected $primaryKey = 'orderID';

    protected $fillable = [
        'userID', 'deliveryAdd', 'city', 'province',
        'country', 'postal', 'contactNo',
        'paymentMethod', 'gcashName', 'gcashNumber', 'referenceNumber', 'amountPaid', 'paymentStatus', 'status',
        'deliveryFee', 'subtotal', 'total',
    ];

    protected $casts = [
        'deliveryFee' => 'decimal:2',
        'subtotal'    => 'decimal:2',
        'total'       => 'decimal:2',
    ];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public static function allStatuses(): array
    {
        return [
            self::STATUS_PENDING, self::STATUS_ACCEPTED,
            self::STATUS_SHIPPED, self::STATUS_DELIVERED,
            self::STATUS_CANCELED,
        ];
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING   => '#f97316',
            self::STATUS_ACCEPTED  => '#3b82f6',
            self::STATUS_SHIPPED   => '#8b5cf6',
            self::STATUS_DELIVERED => '#22c55e',
            self::STATUS_CANCELED  => '#ef4444',
            default                => '#6b7280',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'orderID', 'orderID');
    }
}
