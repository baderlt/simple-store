<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'is_active'
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
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    // Ajoutez cette méthode pour compter les promotions actives
    public function getActiveDiscountsCountAttribute(): int
    {
        return $this->activeDiscounts()->count();
    }

    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }
}