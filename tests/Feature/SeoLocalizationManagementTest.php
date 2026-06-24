<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class SeoLocalizationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_arabic_name_is_optional_and_locale_aware(): void
    {
        $fallbackCategory = Category::create([
            'name' => 'Miels',
            'slug' => 'miels',
            'is_active' => true,
        ]);
        $arabicCategory = Category::create([
            'name' => 'Oils',
            'name_ar' => 'الزيوت',
            'slug' => 'oils',
            'is_active' => true,
        ]);

        App::setLocale('ar');

        $this->assertSame('Miels', $fallbackCategory->localized_name);
        $this->assertSame('الزيوت', $arabicCategory->localized_name);
    }

    public function test_banner_arabic_content_falls_back_to_default_content(): void
    {
        $banner = Banner::create([
            'title' => 'Natural products',
            'title_ar' => 'منتجات طبيعية',
            'description' => 'Default description',
            'cta_text' => 'Shop now',
            'cta_link' => route('products.index'),
            'image_path' => 'banners/example.webp',
            'position' => 'hero',
            'is_active' => true,
        ]);

        App::setLocale('ar');

        $this->assertSame('منتجات طبيعية', $banner->localized_title);
        $this->assertSame('Default description', $banner->localized_description);
        $this->assertSame('Shop now', $banner->localized_cta_text);
    }

    public function test_product_seo_fields_have_safe_fallbacks(): void
    {
        $category = Category::create([
            'name' => 'Miels',
            'slug' => 'miels',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Miel de thym 500g',
            'slug' => 'miel-de-thym-500g',
            'description' => 'Miel naturel premium de Wany Bio.',
            'price' => 300,
            'stock_quantity' => 4,
            'low_stock_alert' => 1,
            'is_active' => true,
        ])->load('category');

        $this->assertSame('Miel de thym 500g | Miels | Wany Bio', $product->seo_meta_title);
        $this->assertSame('Miel naturel premium de Wany Bio.', $product->seo_meta_description);
        $this->assertSame(route('products.show', 'miel-de-thym-500g'), $product->seo_canonical_url);
    }

    public function test_sitemap_contains_active_products_and_categories(): void
    {
        $category = Category::create([
            'name' => 'Miels',
            'slug' => 'miels',
            'is_active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Miel de thym 500g',
            'slug' => 'miel-de-thym-500g',
            'price' => 300,
            'stock_quantity' => 4,
            'low_stock_alert' => 1,
            'is_active' => true,
        ]);

        $this->get(route('sitemap.index'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('categories.show', $category), false)
            ->assertSee(route('products.show', 'miel-de-thym-500g'), false);
    }
}
