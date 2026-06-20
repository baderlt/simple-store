<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\StoreSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontArabicPresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_arabic_checkout_uses_dirham_and_highlights_free_delivery_progress(): void
    {
        $settings = app(StoreSettingsService::class);
        $settings->set('delivery_fee', 35, 'number');
        $settings->set('free_delivery_threshold', 726, 'number');

        $product = $this->createProduct();
        $this->post(route('cart.add', $product), ['quantity' => 1])->assertRedirect();

        $this->withSession(['locale' => 'ar'])
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('أضف 701 درهم للاستفادة من التوصيل المجاني.')
            ->assertSee('25 درهم')
            ->assertSee('35 درهم')
            ->assertDontSee('25 DH')
            ->assertSee('font-extrabold leading-6', false)
            ->assertSee('currency:', false)
            ->assertDontSee("currency: 'DH'", false);
    }

    public function test_footer_hours_and_arabic_desktop_navigation_use_stable_structure(): void
    {
        app(StoreSettingsService::class)->set(
            'working_hours',
            'Matin Lun-Dim: 12h00-15h00 | Soir Lun-Dim: 18h00-00h00'
        );

        $this->withSession(['locale' => 'ar'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('class="desktop-nav -mt-1 hidden lg:flex items-center justify-center"', false)
            ->assertSee('class="desktop-nav-item nav-link', false)
            ->assertSee('class="desktop-nav-item-label"', false)
            ->assertSee('flex: 0 0 9.75rem;', false)
            ->assertSee('column-gap: 0.625rem;', false)
            ->assertDontSee('desktop-nav -mt-1 hidden lg:flex items-center justify-center space-x-8', false)
            ->assertSee('class="flex items-start gap-3"', false)
            ->assertSee('class="flex items-center gap-3"', false)
            ->assertSee('fa-map-marker-alt shrink-0', false)
            ->assertSee('fa-phone shrink-0', false)
            ->assertSee('fa-envelope shrink-0', false)
            ->assertSee('Matin Lun-Dim')
            ->assertSee('12h00-15h00')
            ->assertSee('Soir Lun-Dim')
            ->assertSee('18h00-00h00')
            ->assertSee('whitespace-nowrap font-bold text-white', false);
    }

    public function test_arabic_product_card_category_uses_a_real_icon_gap(): void
    {
        $this->createProduct();

        $this->withSession(['locale' => 'ar'])
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('inline-flex max-w-full items-center gap-1.5', false)
            ->assertSee('class="fas fa-tag shrink-0"', false)
            ->assertDontSee('class="fas fa-tag mr-1.5"', false);
    }

    public function test_arabic_product_delivery_information_is_bold(): void
    {
        $product = $this->createProduct();
        app(StoreSettingsService::class)->set('delivery_fee', 35, 'number');

        $this->withSession(['locale' => 'ar'])
            ->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('التوصيل خلال 24 إلى 72 ساعة')
            ->assertSee('العيون: توصيل مجاني')
            ->assertSee('المدن الأخرى: 35 درهم')
            ->assertSee('text-sm font-bold text-gray-800', false)
            ->assertSee('font-extrabold text-emerald-700', false);
    }

    private function createProduct(): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'general'],
            ['name' => 'General', 'is_active' => true]
        );

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Arabic presentation product',
            'slug' => 'arabic-presentation-product',
            'price' => 25,
            'stock_quantity' => 10,
            'low_stock_alert' => 1,
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        app(StoreSettingsService::class)->flush();

        parent::tearDown();
    }
}
