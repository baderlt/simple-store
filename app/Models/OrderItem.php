<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'discount_price',
        'quantity',
        'subtotal'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the order that owns the item
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the final unit price (with discount if applicable)
     */
    public function getFinalUnitPriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Get the discount amount per unit
     */
    public function getDiscountAmountAttribute()
    {
        if ($this->discount_price) {
            return $this->price - $this->discount_price;
        }
        return 0;
    }

    /**
     * Check if item has discount
     */
    public function hasDiscount(): bool
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }
}