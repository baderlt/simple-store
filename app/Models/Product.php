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
            ->active();
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
        $currentVariant = $this->resolveCurrentVariant($variant);

        if ($currentVariant) {
            return (float) $currentVariant->price;
        }

        return (float) $this->price;
    }

    public function getCurrentStock(?ProductVariant $variant = null): int
    {
        $currentVariant = $this->resolveCurrentVariant($variant);

        if ($currentVariant) {
            return (int) $currentVariant->stock_quantity;
        }

        return (int) $this->stock_quantity;
    }

    public function inStock(?ProductVariant $variant = null): bool
    {
        return $this->getCurrentStock($variant) > 0;
    }

    public function getDiscountedPrice(float $basePrice): float
    {
        $discount = $this->resolveActiveDiscount();

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
        return $this->resolveActiveDiscount() !== null;
    }

    public function getActiveDiscountAttribute()
    {
        return $this->resolveActiveDiscount();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithStorefrontRelations($query)
    {
        return $query->with([
            'category.activeDiscounts',
            'primaryImage',
            'activeDiscount',
            'defaultVariant',
            'variants.items.attribute',
            'variants.items.value',
        ]);
    }

    public function scopeWithActiveDiscounts($query)
    {
        return $query->with(['activeDiscount', 'category.activeDiscounts']);
    }

    public function scopeWithAnyActiveDiscount($query)
    {
        return $query->where(function ($query) {
            $query->whereHas('discounts', fn ($discountQuery) => $discountQuery->active())
                ->orWhereHas('category.discounts', fn ($discountQuery) => $discountQuery->active());
        });
    }

    public function resolveActiveDiscount(): ?Discount
    {
        if ($this->relationLoaded('activeDiscount')) {
            $productDiscount = $this->getRelation('activeDiscount');

            if ($productDiscount) {
                return $productDiscount;
            }
        } elseif (! $this->relationLoaded('discounts')) {
            $productDiscount = $this->discounts()->active()->first();

            if ($productDiscount) {
                return $productDiscount;
            }
        }

        if ($this->relationLoaded('discounts')) {
            $now = now();
            $productDiscount = $this->discounts
                ->first(fn (Discount $discount): bool => $discount->is_active
                    && $discount->start_date <= $now
                    && $discount->end_date >= $now);

            if ($productDiscount) {
                return $productDiscount;
            }
        }

        $category = $this->relationLoaded('category') ? $this->category : $this->category()->first();

        if (! $category) {
            return null;
        }

        if ($category->relationLoaded('activeDiscounts') || $category->relationLoaded('discounts')) {
            return $category->loadedActiveDiscounts()->first();
        }

        return $category->activeDiscounts()->first();
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

    private function resolveCurrentVariant(?ProductVariant $variant = null): ?ProductVariant
    {
        if ($variant) {
            return $variant;
        }

        if (! $this->usesVariants()) {
            return null;
        }

        $defaultVariant = $this->relationLoaded('defaultVariant')
            ? $this->defaultVariant
            : $this->defaultVariant()->first();

        if (! $defaultVariant && $this->relationLoaded('variants')) {
            return $this->variants->firstWhere('is_active', true);
        }

        return $defaultVariant;
    }
}
