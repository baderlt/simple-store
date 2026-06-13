<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardBestSellingProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_best_selling_products_query_uses_a_strict_mysql_compatible_subquery(): void
    {
        $query = Product::withSum('orderItems as total_sold', 'quantity')
            ->orderByDesc('total_sold')
            ->take(6);

        $this->assertStringNotContainsString('group by', strtolower($query->toSql()));
        $this->assertStringContainsString('sum(', strtolower($query->toSql()));
    }

    public function test_best_selling_products_are_ranked_by_the_total_ordered_quantity(): void
    {
        $category = Category::create([
            'name' => 'General',
            'slug' => 'general',
            'is_active' => true,
        ]);
        $firstProduct = $this->createProduct($category, 'First Product');
        $bestSeller = $this->createProduct($category, 'Best Seller');
        $order = Order::create([
            'order_number' => 'ORD-DASHBOARD',
            'customer_name' => 'Test Customer',
            'customer_phone' => '0600000000',
            'customer_address' => 'Test address',
            'customer_city' => 'Casablanca',
            'subtotal' => 400,
            'discount_amount' => 0,
            'delivery_fee' => 0,
            'total' => 400,
            'status' => 'pending',
            'payment_method' => 'cash_on_delivery',
        ]);

        $order->items()->createMany([
            [
                'product_id' => $firstProduct->id,
                'product_name' => $firstProduct->name,
                'price' => 100,
                'quantity' => 1,
                'subtotal' => 100,
            ],
            [
                'product_id' => $bestSeller->id,
                'product_name' => $bestSeller->name,
                'price' => 100,
                'quantity' => 3,
                'subtotal' => 300,
            ],
        ]);

        $products = Product::withSum('orderItems as total_sold', 'quantity')
            ->orderByDesc('total_sold')
            ->get();

        $this->assertSame($bestSeller->id, $products->first()->id);
        $this->assertSame(3, (int) $products->first()->total_sold);
    }

    private function createProduct(Category $category, string $name): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'slug' => str($name)->slug(),
            'price' => 100,
            'stock_quantity' => 10,
            'low_stock_alert' => 2,
            'is_active' => true,
        ]);
    }
}
