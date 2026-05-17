<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'low_stock_alert',
        'sku',
        'brand',
        'product_type',
        'track_stock',
        'digital_file_path',
        'attributes',
        'specifications',
        'variants',
        'localized_content',
        'seo',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'track_stock' => 'boolean',
        'attributes' => 'array',
        'specifications' => 'array',
        'variants' => 'array',
        'localized_content' => 'array',
        'seo' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected $appends = ['final_price'];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }

    public function activeDiscount(): HasOne
    {
        return $this->hasOne(Discount::class)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class);
    }

    public function getFinalPriceAttribute()
    {
        $discount = $this->activeDiscount;

        if (! $discount && $this->category) {
            $discount = $this->category->activeDiscounts()->first();
        }

        if ($discount) {
            return round($this->price - (($this->price * $discount->discount_percentage) / 100), 2);
        }

        return $this->price;
    }

    public function hasDiscount(): bool
    {
        return (bool) $this->activeDiscount || (bool) $this->category?->activeDiscounts()->exists();
    }

    public function getActiveDiscountAttribute()
    {
        return $this->discounts()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first()
            ?: $this->category?->activeDiscounts()->first();
    }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->stock_quantity <= $this->low_stock_alert;
    }

    public function isDigital(): bool
    {
        return $this->product_type === 'digital';
    }
}
