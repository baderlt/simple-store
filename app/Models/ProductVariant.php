<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'sku', 'price', 'stock_quantity', 'image_path', 'is_default'];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_default' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductVariantItem::class);
    }

    public function getOptionLabelAttribute(): string
    {
        return $this->items
            ->map(fn ($item) => optional($item->attribute)->name . ': ' . optional($item->value)->value)
            ->filter()
            ->implode(' / ');
    }

    public function getOptionValuesAttribute(): array
    {
        return $this->items
            ->mapWithKeys(fn ($item) => [optional($item->attribute)->name => optional($item->value)->value])
            ->filter()
            ->all();
    }

    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }
}
