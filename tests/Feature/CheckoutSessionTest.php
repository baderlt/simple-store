<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_normal_checkout_discards_an_abandoned_direct_checkout_product(): void
    {
        $cartProduct = $this->createProduct('Cart Product', 'cart-product');
        $directProduct = $this->createProduct('Direct Product', 'direct-product');

        $this->post(route('cart.add', $cartProduct), ['quantity' => 1])->assertRedirect();
        $this->get(route('checkout.direct', ['id' => $directProduct->id, 'quantity' => 1]))
            ->assertRedirect(route('checkout.index', ['direct' => 1]));

        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee($cartProduct->name)
            ->assertDontSee($directProduct->name);

        $this->assertNull(session('direct_checkout'));
    }

    public function test_cart_order_is_used_when_an_abandoned_direct_checkout_session_exists(): void
    {
        $cartProduct = $this->createProduct('Cart Product', 'cart-product');
        $directProduct = $this->createProduct('Direct Product', 'direct-product');

        $this->post(route('cart.add', $cartProduct), ['quantity' => 1])->assertRedirect();
        $this->get(route('checkout.direct', ['id' => $directProduct->id, 'quantity' => 1]))->assertRedirect();

        $this->post(route('checkout.store'), [
            'customer_name' => 'Cart Customer',
            'customer_phone' => '0600000000',
            'customer_address' => '1 Cart Street',
            'customer_city' => 'Casablanca',
            'is_direct_checkout' => 0,
        ])->assertRedirect();

        $order = Order::with('items')->firstOrFail();

        $this->assertSame([$cartProduct->id], $order->items->pluck('product_id')->all());
        $this->assertNull(session('direct_checkout'));
        $this->assertNull(session('cart'));
    }

    private function createProduct(string $name, string $slug): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'general'],
            ['name' => 'General', 'is_active' => true]
        );

        return Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'slug' => $slug,
            'price' => 25,
            'stock_quantity' => 5,
            'low_stock_alert' => 1,
            'is_active' => true,
        ]);
    }
}
