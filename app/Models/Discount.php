<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    protected $fillable = [
        'product_id',
        'category_id',
        'discount_percentage',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function getIsCurrentlyActiveAttribute(): bool
    {
        return $this->is_active && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }
    public function activeDiscount()
{
    return $this->hasOne(Discount::class)
        ->where('is_active', true)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now());
}

}