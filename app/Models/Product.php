<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // APPEND ATTRIBUTE TO ALWAYS INCLUDE
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

    // FIXED: Get final price with discount
    public function getFinalPriceAttribute()
    {
        // Check for product-specific discount
        $discount = $this->activeDiscount;
        
        // If no product discount, check category discount
        if (!$discount && $this->category) {
            $discount = $this->category->activeDiscounts()->first();
        }
        
        if ($discount) {
            $discountAmount = ($this->price * $discount->discount_percentage) / 100;
            return round($this->price - $discountAmount, 2);
        }
        
        return $this->price;
    }

    // Check if product has any active discount
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

    // Get the active discount (product or category)
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

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_alert;
    }
}
