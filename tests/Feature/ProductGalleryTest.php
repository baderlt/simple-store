<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_primary_image_is_the_initial_and_selected_gallery_image(): void
    {
        $category = Category::create([
            'name' => 'Gallery',
            'slug' => 'gallery',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Gallery Product',
            'slug' => 'gallery-product',
            'price' => 50,
            'stock_quantity' => 5,
            'low_stock_alert' => 1,
            'is_active' => true,
        ]);
        $product->images()->create([
            'image_path' => 'products/first-by-order.jpg',
            'is_primary' => false,
            'order' => 1,
        ]);
        $product->images()->create([
            'image_path' => 'products/primary.jpg',
            'is_primary' => true,
            'order' => 2,
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk()
            ->assertSeeInOrder([
                'src="http://localhost/storage/products/primary.jpg"',
                'onclick="changeMainImage(\'http://localhost/storage/products/primary.jpg\', 1)"',
                'onclick="changeMainImage(\'http://localhost/storage/products/first-by-order.jpg\', 2)"',
            ], false)
            ->assertSee('object-contain', false)
            ->assertSee('hidden sm:block text-center', false)
            ->assertDontSee('id="mobileBuyNowBar"', false)
            ->assertSee("syncGallerySelection(galleryIndex >= 0 ? galleryIndex + 1 : 0);", false);
    }
}
