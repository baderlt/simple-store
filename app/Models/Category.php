<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Category extends Model
{
    protected $fillable = [
        'name', 'name_ar', 'slug', 'description', 'image', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }

    // Cette méthode retourne une query builder, pas une relation
    public function activeDiscounts()
    {
        return $this->discounts()
            ->active();
    }

    // Ajoutez cette méthode pour compter les promotions actives
    public function getActiveDiscountsCountAttribute(): int
    {
        if (array_key_exists('active_discounts_count', $this->attributes)) {
            return (int) $this->attributes['active_discounts_count'];
        }

        if ($this->relationLoaded('discounts')) {
            return $this->loadedActiveDiscounts()->count();
        }

        return $this->activeDiscounts()->count();
    }

    public function getProductsCountAttribute(): int
    {
        if (array_key_exists('products_count', $this->attributes)) {
            return (int) $this->attributes['products_count'];
        }

        if ($this->relationLoaded('products')) {
            return $this->products->count();
        }

        return $this->products()->count();
    }

    public function loadedActiveDiscounts(): Collection
    {
        if ($this->relationLoaded('activeDiscounts')) {
            return $this->activeDiscounts;
        }

        if (! $this->relationLoaded('discounts')) {
            return collect();
        }

        $now = now();

        return $this->discounts
            ->filter(fn (Discount $discount): bool => $discount->is_active
                && $discount->start_date <= $now
                && $discount->end_date >= $now)
            ->values();
    }

    public function getLocalizedNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && filled($this->name_ar)) {
            return $this->name_ar;
        }

        return $this->name;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return $this->description;
    }
}
