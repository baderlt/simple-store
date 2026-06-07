<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'variant_snapshot',
        'price',
        'discount_price',
        'quantity',
        'subtotal'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'variant_snapshot' => 'array',
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

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $suffix = $this->variant_snapshot ? implode(' / ', array_values($this->variant_snapshot)) : '';

        return $suffix ? $this->product_name . ' - ' . $suffix : $this->product_name;
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