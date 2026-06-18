<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_a_product_outside_the_current_pagination_page(): void
    {
        [$admin, $category] = $this->adminAndCategory();
        $target = $this->createProduct($category, 'Hidden Pagination Product', 'hidden-pagination-product');
        $target->forceFill(['created_at' => now()->subDay(), 'updated_at' => now()->subDay()])->save();

        foreach (range(1, 20) as $index) {
            $this->createProduct($category, "Recent Product {$index}", "recent-product-{$index}");
        }

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertDontSee('Hidden Pagination Product');

        $this->actingAs($admin)
            ->get(route('admin.products.index', ['search' => 'Hidden Pagination']))
            ->assertOk()
            ->assertSee('Hidden Pagination Product')
            ->assertViewHas('products', function ($products) use ($target) {
                return $products->total() === 1
                    && $products->first()?->is($target);
            });
    }

    public function test_search_can_find_a_product_by_variant_sku(): void
    {
        [$admin, $category] = $this->adminAndCategory();
        $product = $this->createProduct($category, 'Variant Product', 'variant-product');

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'VARIANT-SEARCH-42',
            'price' => 35,
            'stock_quantity' => 5,
            'is_default' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.index', ['search' => 'VARIANT-SEARCH-42']))
            ->assertOk()
            ->assertSee('Variant Product')
            ->assertViewHas('products', fn ($products) => $products->total() === 1);
    }

    private function adminAndCategory(): array
    {
        return [
            User::factory()->create(['is_admin' => true]),
            Category::create([
                'name' => 'General',
                'slug' => 'general',
                'is_active' => true,
            ]),
        ];
    }

    private function createProduct(Category $category, string $name, string $slug): Product
    {
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
