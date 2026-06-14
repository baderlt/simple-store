<?php
// app/Models/Order.php - COMPLETE FIXED VERSION
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_city',
        'subtotal',
        'discount_amount',
        'delivery_fee',
        'total',
        'status',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate a unique order number
     * Format: ORD-0001, ORD-0002, ...
     */
    public static function generateOrderNumber(): string
    {
        $lastSequence = self::query()
            ->where('order_number', 'like', 'ORD-%')
            ->pluck('order_number')
            ->reduce(function (int $highest, string $orderNumber): int {
                if (! preg_match('/^ORD-(\d+)$/', $orderNumber, $matches)) {
                    return $highest;
                }

                return max($highest, (int) $matches[1]);
            }, 0);

        return 'ORD-' . str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'preparing' => 'bg-blue-100 text-blue-800',
            'out_for_delivery' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => __('status_pending'),
            'preparing' => __('status_preparing'),
            'out_for_delivery' => __('status_out_for_delivery'),
            'delivered' => __('status_delivered'),
            'cancelled' => __('status_cancelled'),
            default => __('status_unknown'),
        };
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope for recent orders
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }
}
