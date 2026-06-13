<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStorefrontMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_products_receive_persisted_random_storefront_metrics(): void
    {
        $product = $this->createProduct();

        $this->assertGreaterThanOrEqual(4.2, (float) $product->review_rating);
        $this->assertLessThanOrEqual(4.9, (float) $product->review_rating);
        $this->assertGreaterThanOrEqual(50, $product->sales_count);
        $this->assertLessThanOrEqual(100, $product->sales_count);
        $this->assertGreaterThanOrEqual(10, $product->reviews_count);
        $this->assertLessThanOrEqual(min(80, $product->sales_count), $product->reviews_count);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'review_rating' => $product->review_rating,
            'reviews_count' => $product->reviews_count,
            'sales_count' => $product->sales_count,
        ]);
    }

    public function test_product_metrics_are_not_shown_on_the_minimal_product_page(): void
    {
        $product = $this->createProduct([
            'review_rating' => 4.7,
            'reviews_count' => 42,
            'sales_count' => 68,
        ]);

        foreach (range(1, 2) as $refresh) {
            $this->get(route('products.show', $product->slug))
                ->assertOk()
                ->assertDontSee('4.7 (42 avis)')
                ->assertDontSee('68 vendus')
                ->assertDontSee('Disponible en stock')
                ->assertDontSee('Garantie')
                ->assertDontSee('1 an')
                ->assertDontSee('Retours')
                ->assertDontSee('15 jours')
                ->assertDontSee('Description');
        }

        $this->assertSame(68, $product->fresh()->sales_count);
    }

    public function test_product_page_shows_localized_delivery_information_and_whole_prices(): void
    {
        $product = $this->createProduct(['price' => 125]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('125')
            ->assertDontSee('125.00')
            ->assertSee('dir="ltr"', false)
            ->assertDontSee("Jusqu'au")
            ->assertSee('id="mobileBuyNowBar"', false)
            ->assertDontSee('translate-y-full', false)
            ->assertSee('add-to-cart-btn w-full bg-gray-950', false)
            ->assertSee('buy-now-btn order-now-attention', false)
            ->assertSee('function updateDisplayedTotal(quantity)', false)
            ->assertSee(__('product.delivery_title'))
            ->assertSee(__('product.delivery_time'))
            ->assertSee(__('product.delivery_free_city', ['city' => 'Laâyoune']))
            ->assertSee(__('product.delivery_other_cities', ['price' => '40']));
    }

    public function test_checkout_increments_sales_once_per_product_regardless_of_quantity(): void
    {
        $product = $this->createProduct([
            'stock_quantity' => 10,
            'sales_count' => 68,
        ]);

        $this->post(route('cart.add', $product->id), ['quantity' => 3])->assertRedirect();

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Store Customer',
            'customer_phone' => '0600000000',
            'customer_address' => '1 Test Street',
            'customer_city' => 'Casablanca',
        ]);

        $response->assertRedirect();
        $this->assertSame(69, $product->fresh()->sales_count);
        $this->assertSame(7, $product->fresh()->stock_quantity);
    }

    private function createProduct(array $overrides = []): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'general'],
            ['name' => 'General', 'is_active' => true]
        );

        return Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Persistent Metrics Product',
            'slug' => 'persistent-metrics-product',
            'price' => 25,
            'stock_quantity' => 5,
            'low_stock_alert' => 1,
            'is_active' => true,
        ], $overrides));
    }
}
