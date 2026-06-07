<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price',
        'stock_quantity', 'low_stock_alert', 'sku', 'is_active', 'is_featured'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_alert' => 'integer',
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

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->with(['items.attribute', 'items.value']);
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true)->with(['items.attribute', 'items.value']);
    }

    public function usesVariants(): bool
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->isNotEmpty();
        }

        return $this->variants()->exists();
    }

    public function getCurrentPrice(?ProductVariant $variant = null): float
    {
        if ($variant) {
            return (float) $variant->price;
        }

        if ($this->usesVariants()) {
            $defaultVariant = $this->relationLoaded('defaultVariant') ? $this->defaultVariant : $this->defaultVariant()->first();
            if (!$defaultVariant && $this->relationLoaded('variants')) {
                $defaultVariant = $this->variants->first();
            }
            if ($defaultVariant) {
                return (float) $defaultVariant->price;
            }
        }

        return (float) $this->price;
    }

    public function getCurrentStock(?ProductVariant $variant = null): int
    {
        if ($variant) {
            return (int) $variant->stock_quantity;
        }

        if ($this->usesVariants()) {
            $defaultVariant = $this->relationLoaded('defaultVariant') ? $this->defaultVariant : $this->defaultVariant()->first();
            if (!$defaultVariant && $this->relationLoaded('variants')) {
                $defaultVariant = $this->variants->first();
            }
            if ($defaultVariant) {
                return (int) $defaultVariant->stock_quantity;
            }
        }

        return (int) $this->stock_quantity;
    }

    public function inStock(?ProductVariant $variant = null): bool
    {
        return $this->getCurrentStock($variant) > 0;
    }

    public function getDiscountedPrice(float $basePrice): float
    {
        $discount = $this->activeDiscount;

        if (!$discount && $this->category) {
            $discount = $this->category->activeDiscounts()->first();
        }

        if ($discount) {
            $discountAmount = ($basePrice * $discount->discount_percentage) / 100;
            return round($basePrice - $discountAmount, 2);
        }

        return round($basePrice, 2);
    }

    public function getFinalPriceAttribute()
    {
        return $this->getDiscountedPrice((float) $this->price);
    }

    public function hasDiscount(): bool
    {
        if ($this->activeDiscount) {
            return true;
        }

        if ($this->category) {
            return $this->category->activeDiscounts()->exists();
        }

        return false;
    }

    public function getActiveDiscountAttribute()
    {
        $productDiscount = $this->discounts()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if ($productDiscount) {
            return $productDiscount;
        }

        if ($this->category) {
            return $this->category->activeDiscounts()->first();
        }

        return null;
    }


    public function scopeAvailable($query)
    {
        return $query->where(function ($stockQuery) {
            $stockQuery->where('stock_quantity', '>', 0)
                ->orWhereHas('variants', fn ($variantQuery) => $variantQuery->where('stock_quantity', '>', 0));
        });
    }

    public function isLowStock(?ProductVariant $variant = null): bool
    {
        return $this->getCurrentStock($variant) <= $this->low_stock_alert;
    }
}
