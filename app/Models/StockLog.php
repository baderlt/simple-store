<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'quantity_change',
        'quantity_after',
        'type',
        'notes'
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'quantity_after' => 'integer',
    ];

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made the change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get type badge color
     */
    public function getTypeBadgeAttribute(): string
    {
        return match($this->type) {
            'purchase' => 'bg-green-100 text-green-800',
            'sale' => 'bg-blue-100 text-blue-800',
            'adjustment' => 'bg-yellow-100 text-yellow-800',
            'return' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Scope for filtering by type
     */
    public function scopeByType($query, $type)
    {
        if ($type) {
            return $query->where('type', $type);
        }
        return $query;
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $limit = 50)
    {
        return $query->latest()->limit($limit);
    }
}
