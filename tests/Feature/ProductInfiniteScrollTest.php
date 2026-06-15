<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductInfiniteScrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_page_only_renders_the_first_batch_and_exposes_the_next_page(): void
    {
        $this->createProducts(13);

        $response = $this->get(route('products.index'));

        $response->assertOk()
            ->assertViewHas('products', fn ($products) => $products->count() === 12)
            ->assertSee('id="productsGrid"', false)
            ->assertSee('data-next-page-url=', false);
    }

    public function test_next_product_batch_is_returned_as_json_for_infinite_scroll(): void
    {
        $this->createProducts(13);

        $response = $this->getJson(route('products.index', ['page' => 2]));

        $response->assertOk()
            ->assertJsonPath('next_page_url', null)
            ->assertJsonStructure(['html', 'next_page_url']);

        $this->assertSame(1, substr_count($response->json('html'), 'group relative bg-gradient-to-br'));
    }

    private function createProducts(int $count): void
    {
        $category = Category::create([
            'name' => 'General',
            'slug' => 'general',
            'is_active' => true,
        ]);

        foreach (range(1, $count) as $number) {
            Product::create([
                'category_id' => $category->id,
                'name' => "Product {$number}",
                'slug' => "product-{$number}",
                'price' => $number,
                'stock_quantity' => 10,
                'low_stock_alert' => 2,
                'is_active' => true,
            ]);
        }
    }
}
