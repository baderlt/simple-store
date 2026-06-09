<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_show_page_always_receives_status_statistics(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->createProduct();

        $response = $this->actingAs($admin)->get(route('admin.products.show', $product));

        $response->assertOk();
        $response->assertViewHas('ordersData', function (array $ordersData) {
            return array_key_exists('status_stats', $ordersData)
                && $ordersData['status_stats']->isEmpty();
        });
    }

    public function test_product_show_status_statistics_include_each_order_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->createProduct();
        $pendingOrder = $this->createOrder('pending', 'ORD-PENDING');
        $cancelledOrder = $this->createOrder('cancelled', 'ORD-CANCELLED');

        $pendingOrder->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => 80,
            'quantity' => 2,
            'subtotal' => 160,
        ]);
        $cancelledOrder->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => 80,
            'quantity' => 1,
            'subtotal' => 80,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.show', $product));

        $response->assertOk();
        $response->assertViewHas('ordersData', function (array $ordersData) {
            $stats = $ordersData['status_stats']->keyBy('status');

            return $stats->keys()->sort()->values()->all() === ['cancelled', 'pending']
                && $stats['pending']->order_count === 1
                && $stats['pending']->total_quantity === 2
                && (float) $stats['pending']->total_revenue === 160.0;
        });
    }

    private function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'General',
            'slug' => 'general',
            'is_active' => true,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Pure Honey',
            'slug' => 'pure-honey',
            'price' => 80,
            'stock_quantity' => 10,
            'low_stock_alert' => 2,
            'is_active' => true,
        ]);
    }

    private function createOrder(string $status, string $number): Order
    {
        return Order::create([
            'order_number' => $number,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0600000000',
            'customer_address' => 'Test address',
            'customer_city' => 'Casablanca',
            'subtotal' => 160,
            'discount_amount' => 0,
            'delivery_fee' => 0,
            'total' => 160,
            'status' => $status,
            'payment_method' => 'cash_on_delivery',
        ]);
    }
}
