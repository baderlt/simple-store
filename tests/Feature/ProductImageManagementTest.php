<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_keeps_selected_images_attached_for_submission(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Category::create(['name' => 'General', 'slug' => 'general', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.products.create'));

        $response->assertOk();
        $response->assertSee('enctype="multipart/form-data"', false);
        $response->assertSee('updateHiddenFields();', false);
        $response->assertDontSee("event.target.value = '';", false);
    }

    public function test_admin_can_upload_multiple_images_and_choose_the_primary_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'General', 'slug' => 'general', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Image Product',
            'category_id' => $category->id,
            'description' => 'Image upload test',
            'price' => 100,
            'stock_quantity' => 5,
            'low_stock_alert' => 1,
            'is_active' => 1,
            'primary_image_index' => 1,
            'images' => [
                UploadedFile::fake()->image('first.jpg'),
                UploadedFile::fake()->image('primary.jpg'),
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::where('slug', 'image-product')->firstOrFail();
        $this->assertCount(2, $product->images);
        $this->assertSame(1, $product->primaryImage()->firstOrFail()->order);
        $product->images->each(fn (ProductImage $image) => Storage::disk('public')->assertExists($image->image_path));
    }

    public function test_primary_and_delete_routes_are_scoped_to_the_product(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'General', 'slug' => 'general', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Gallery Product',
            'slug' => 'gallery-product',
            'price' => 50,
            'stock_quantity' => 2,
            'low_stock_alert' => 1,
            'is_active' => true,
        ]);
        $otherProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Other Product',
            'slug' => 'other-product',
            'price' => 50,
            'stock_quantity' => 2,
            'low_stock_alert' => 1,
            'is_active' => true,
        ]);
        Storage::disk('public')->put('products/first.jpg', 'first');
        Storage::disk('public')->put('products/second.jpg', 'second');
        $first = $product->images()->create(['image_path' => 'products/first.jpg', 'is_primary' => true, 'order' => 0]);
        $second = $product->images()->create(['image_path' => 'products/second.jpg', 'is_primary' => false, 'order' => 1]);

        $this->actingAs($admin)
            ->patchJson(route('admin.products.images.set-primary', [$product, $second]))
            ->assertOk()
            ->assertJson(['success' => true]);
        $this->assertFalse($first->fresh()->is_primary);
        $this->assertTrue($second->fresh()->is_primary);

        $this->actingAs($admin)
            ->deleteJson(route('admin.products.images.delete', [$product, $second]))
            ->assertOk()
            ->assertJson(['success' => true]);
        $this->assertTrue($first->fresh()->is_primary);
        Storage::disk('public')->assertMissing('products/second.jpg');

        $this->actingAs($admin)
            ->deleteJson(route('admin.products.images.delete', [$otherProduct, $first]))
            ->assertNotFound();
    }
}
