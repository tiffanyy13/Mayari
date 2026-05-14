<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'orderitems';

    protected $primaryKey = 'orderItemID';

    protected $fillable = [
        'orderID', 'productID', 'quantity', 'variant', 'unitPrice',
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

    /**
     * Label for color/shade column: stored choice, single-option inference, or placeholder when missing.
     * Returns null only when the product has no variant options in the catalog.
     */
    public function variantLabelForDisplay(): ?string
    {
        $product = $this->product;
        if (!$product || !is_array($product->variants) || $product->variants === []) {
            return null;
        }
        $opts = array_values(array_filter(
            array_map(static fn ($v) => trim((string) $v), $product->variants),
            static fn ($v) => $v !== ''
        ));
        if ($opts === []) {
            return null;
        }

        $stored = is_string($this->variant) ? trim($this->variant) : '';
        if ($stored !== '') {
            return $stored;
        }
        if (count($opts) === 1) {
            return $opts[0];
        }

        // Multi-shade product but nothing stored (older checkouts).
        return 'N/A';
    }

    /** One line per item for lists, modals, and PDFs. */
    public function summaryLine(): string
    {
        $name = $this->product->pName ?? 'Product';
        $qty = (int) $this->quantity;
        $label = $this->variantLabelForDisplay();
        if ($label !== null) {
            $nbsp = "\u{202F}"; // narrow no-break space so ") ×" does not wrap awkwardly

            return "{$name} ({$label}){$nbsp}×{$qty}";
        }

        return "{$name} ×{$qty}";
    }
}