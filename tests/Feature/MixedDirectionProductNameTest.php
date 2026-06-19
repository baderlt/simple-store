<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MixedDirectionProductNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_mixed_arabic_latin_product_names_are_direction_isolated_across_the_storefront(): void
    {
        $category = Category::create([
            'name' => 'Produits naturels',
            'slug' => 'produits-naturels',
            'is_active' => true,
        ]);

        $latinFirstName = 'GINSENG - الجنسينغ - 1 gramme x10';
        $arabicFirstName = 'املو بذور اليقطين (زريعة الكرعة) - 750g / بالعسل';

        $latinFirstProduct = $this->createProduct($category, $latinFirstName, 'ginseng-mixed-name');
        $arabicFirstProduct = $this->createProduct($category, $arabicFirstName, 'amlo-mixed-name');

        $listing = $this->withSession(['locale' => 'ar'])->get(route('products.index'));
        $listing->assertOk()
            ->assertSee($latinFirstName)
            ->assertSee($arabicFirstName)
            ->assertSee('bidi-auto bidi-auto-block', false)
            ->assertSee('unicode-bidi: plaintext;', false)
            ->assertSee('class="search-suggestion-name bidi-auto" dir="auto"', false);

        $this->withSession(['locale' => 'ar'])
            ->get(route('products.show', $arabicFirstProduct->slug))
            ->assertOk()
            ->assertSee($arabicFirstName)
            ->assertSee('class="bidi-auto text-3xl', false)
            ->assertSee('dir="auto"', false);

        $this->post(route('cart.add', $latinFirstProduct), ['quantity' => 1])->assertRedirect();

        $this->withSession(['locale' => 'ar'])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee($latinFirstName)
            ->assertSee('class="bidi-auto font-semibold text-gray-900"', false);

        $this->withSession(['locale' => 'ar'])
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee($latinFirstName)
            ->assertSee('class="bidi-auto font-semibold text-gray-900 text-sm truncate"', false);

        $order = Order::create([
            'order_number' => 'ORD-BIDI-0001',
            'customer_name' => 'Client Test',
            'customer_phone' => '0600000000',
            'customer_address' => 'Adresse test',
            'customer_city' => 'Laâyoune',
            'subtotal' => 25,
            'discount_amount' => 0,
            'delivery_fee' => 0,
            'total' => 25,
            'status' => 'pending',
            'payment_method' => 'cash_on_delivery',
        ]);

        $order->items()->create([
            'product_id' => $arabicFirstProduct->id,
            'product_name' => $arabicFirstName,
            'price' => 25,
            'quantity' => 1,
            'subtotal' => 25,
        ]);

        $this->withSession(['locale' => 'ar'])
            ->get(route('order.success', $order))
            ->assertOk()
            ->assertSee($arabicFirstName)
            ->assertSee('class="bidi-auto" dir="auto"', false);
    }

    private function createProduct(Category $category, string $name, string $slug): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'slug' => $slug,
            'price' => 25,
            'stock_quantity' => 10,
            'low_stock_alert' => 1,
            'is_active' => true,
            'is_featured' => true,
        ]);
    }
}
