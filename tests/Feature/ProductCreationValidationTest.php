<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCreationValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_is_not_created_when_variant_validation_fails(): void
    {
        [$admin, $category] = $this->adminAndCategory();

        $response = $this->actingAs($admin)
            ->from(route('admin.products.create'))
            ->post(route('admin.products.store'), $this->validProductData($category, [
                'has_variants' => 1,
                'variants_payload' => json_encode([
                    'variants' => [
                        [
                            'values' => [],
                            'price_type' => 'fixed',
                            'price' => 25,
                            'stock_quantity' => 3,
                        ],
                    ],
                ]),
            ]));

        $response->assertRedirect(route('admin.products.create'))
            ->assertSessionHasErrors('variants_payload');
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_variants', 0);
    }

    public function test_duplicate_product_name_returns_custom_name_error_without_creating_product(): void
    {
        [$admin, $category] = $this->adminAndCategory();
        Product::create([
            'category_id' => $category->id,
            'name' => 'Existing Product',
            'slug' => 'existing-product',
            'price' => 20,
            'stock_quantity' => 4,
            'low_stock_alert' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.products.create'))
            ->post(route('admin.products.store'), $this->validProductData($category, [
                'name' => 'Existing Product',
            ]));

        $response->assertRedirect(route('admin.products.create'))
            ->assertSessionHasErrors([
                'name' => 'A product with this name already exists. Please choose another name.',
            ]);
        $this->assertDatabaseCount('products', 1);

        $this->actingAs($admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('A product with this name already exists. Please choose another name.');
    }

    public function test_valid_product_creation_still_succeeds(): void
    {
        [$admin, $category] = $this->adminAndCategory();

        $response = $this->actingAs($admin)->post(
            route('admin.products.store'),
            $this->validProductData($category)
        );

        $response->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'slug' => 'new-product',
            'category_id' => $category->id,
        ]);
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

    private function validProductData(Category $category, array $overrides = []): array
    {
        return array_merge([
            'name' => 'New Product',
            'category_id' => $category->id,
            'description' => 'A valid product',
            'price' => 25,
            'stock_quantity' => 3,
            'low_stock_alert' => 1,
            'is_active' => 1,
        ], $overrides);
    }
}
