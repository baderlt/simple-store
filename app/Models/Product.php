<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price',
        'stock_quantity', 'low_stock_alert', 'sku', 'is_active', 'is_featured',
        'review_rating', 'reviews_count', 'sales_count',
        'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
        'og_title', 'og_description', 'og_image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_alert' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'review_rating' => 'decimal:1',
        'reviews_count' => 'integer',
        'sales_count' => 'integer',
    ];

    protected $appends = ['final_price'];

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if ($product->review_rating === null) {
                $product->review_rating = random_int(42, 49) / 10;
            }

            if ($product->sales_count === null) {
                $product->sales_count = random_int(50, 100);
            }

            if ($product->reviews_count === null) {
                $product->reviews_count = random_int(10, min(80, (int) $product->sales_count));
            }
        });
    }

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
        return $this->hasOne(ProductVariant::class)->where('is_default', true)->where('is_active', true)->with(['items.attribute', 'items.value']);
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
                $defaultVariant = $this->variants->firstWhere('is_active', true);
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
                $defaultVariant = $this->variants->firstWhere('is_active', true);
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
            $stockQuery->where(function ($simpleProductQuery) {
                $simpleProductQuery->whereDoesntHave('variants')->where('stock_quantity', '>', 0);
            })->orWhereHas('variants', fn ($variantQuery) => $variantQuery
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0));
        });
    }

    public function isLowStock(?ProductVariant $variant = null): bool
    {
        return $this->getCurrentStock($variant) <= $this->low_stock_alert;
    }

    public function getSeoMetaTitleAttribute(): string
    {
        return Str::limit($this->meta_title ?: $this->fallbackSeoTitle(), 70, '');
    }

    public function getSeoMetaDescriptionAttribute(): string
    {
        return Str::limit($this->meta_description ?: $this->fallbackSeoDescription(), 170, '');
    }

    public function getSeoMetaKeywordsAttribute(): string
    {
        if ($this->meta_keywords) {
            return $this->meta_keywords;
        }

        return collect([
            $this->name,
            $this->category?->localized_name ?? $this->category?->name,
            'Wany Bio',
            'bio',
            'produits naturels',
        ])
            ->filter()
            ->unique()
            ->implode(', ');
    }

    public function getSeoCanonicalUrlAttribute(): string
    {
        return $this->canonical_url ?: route('products.show', $this->slug);
    }

    public function getSeoOgTitleAttribute(): string
    {
        return Str::limit($this->og_title ?: $this->seo_meta_title, 95, '');
    }

    public function getSeoOgDescriptionAttribute(): string
    {
        return Str::limit($this->og_description ?: $this->seo_meta_description, 170, '');
    }

    public function getSeoOgImageUrlAttribute(): string
    {
        if ($this->og_image) {
            return Str::startsWith($this->og_image, ['http://', 'https://'])
                ? $this->og_image
                : asset('storage/' . ltrim($this->og_image, '/'));
        }

        $imagePath = $this->primaryImage?->image_path
            ?: ($this->relationLoaded('images') ? $this->images->first()?->image_path : null);

        return $imagePath
            ? asset('storage/' . $imagePath)
            : asset('img/default-og.jpg');
    }

    public function getPrimaryImageAltAttribute(): string
    {
        return trim(collect([
            $this->name,
            $this->category?->localized_name ?? $this->category?->name,
            'Wany Bio',
        ])->filter()->implode(' - '));
    }

    public function structuredDataImageUrls(): array
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images
            ->pluck('image_path')
            ->filter()
            ->map(fn (string $path) => asset('storage/' . $path))
            ->values()
            ->all() ?: [$this->seo_og_image_url];
    }

    private function fallbackSeoTitle(): string
    {
        return collect([
            $this->name,
            $this->category?->localized_name ?? $this->category?->name,
            'Wany Bio',
        ])->filter()->implode(' | ');
    }

    private function fallbackSeoDescription(): string
    {
        $description = trim(strip_tags((string) $this->description));

        if ($description !== '') {
            return $description;
        }

        $category = $this->category?->localized_name ?? $this->category?->name;
        $price = number_format((float) $this->getCurrentPrice(), 2, '.', '');

        return trim(sprintf(
            '%s%s disponible chez Wany Bio à partir de %s MAD.',
            $this->name,
            $category ? ' - ' . $category : '',
            $price
        ));
    }
}
