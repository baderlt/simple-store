<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_variant_can_be_added_to_cart_with_its_price_and_option_snapshot(): void
    {
        [$product, $variant] = $this->variantProduct();

        $response = $this->post(route('cart.add', $product), ['variant_id' => $variant->id, 'quantity' => 2]);

        $response->assertRedirect();
        $item = session('cart')["product_{$product->id}_variant_{$variant->id}"];
        $this->assertSame($variant->id, $item['variant_id']);
        $this->assertSame(2, $item['quantity']);
        $this->assertSame(120.0, $item['price']);
        $this->assertSame(['Weight' => '1kg'], $item['selected_attributes']);
        $this->assertSame('Pure Honey - 1kg', $item['display_name']);
    }

    public function test_inactive_variant_cannot_be_added_to_cart(): void
    {
        [$product, $variant] = $this->variantProduct(['is_active' => false]);

        $response = $this->postJson(route('cart.add', $product), ['variant_id' => $variant->id]);

        $response->assertStatus(400)->assertJson(['success' => false]);
        $this->assertEmpty(session('cart', []));
    }

    public function test_checkout_records_variant_and_decrements_only_variant_stock(): void
    {
        [$product, $variant] = $this->variantProduct(['stock_quantity' => 5]);
        $this->post(route('cart.add', $product), ['variant_id' => $variant->id]);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Variant Customer',
            'customer_phone' => '0600000000',
            'customer_address' => '1 Test Street',
            'customer_city' => 'Casablanca',
        ]);

        $orderItem = OrderItem::firstOrFail();
        $response->assertRedirect(route('order.success', $orderItem->order_id));
        $this->assertSame($variant->id, $orderItem->product_variant_id);
        $this->assertSame(['Weight' => '1kg'], $orderItem->variant_snapshot);
        $this->assertSame('120.00', $orderItem->price);
        $this->assertSame('150.00', $orderItem->order->total);
        $this->assertSame(4, $variant->fresh()->stock_quantity);
        $this->assertSame(99, $product->fresh()->stock_quantity);
    }


    public function test_product_page_uses_an_in_stock_variant_when_saved_default_is_out_of_stock(): void
    {
        [$product, $outOfStockVariant] = $this->variantProduct(['stock_quantity' => 0]);
        $attribute = ProductAttribute::where('slug', 'weight')->firstOrFail();
        $value = ProductAttributeValue::create([
            'product_attribute_id' => $attribute->id,
            'value' => '500g',
            'slug' => '500g',
        ]);
        $availableVariant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'HONEY-500G',
            'unit' => 'g',
            'price_type' => 'fixed',
            'price' => 70,
            'price_adjustment' => 0,
            'stock_quantity' => 3,
            'is_default' => false,
            'is_active' => true,
        ]);
        ProductVariantItem::create([
            'product_variant_id' => $availableVariant->id,
            'product_attribute_id' => $attribute->id,
            'product_attribute_value_id' => $value->id,
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk()
            ->assertSee('id="variantChooser"', false)
            ->assertSee('id="purchaseActions"', false)
            ->assertSee('data-default-id="' . $availableVariant->id . '"', false)
            ->assertDontSee('data-default-id="' . $outOfStockVariant->id . '"', false);
    }

    private function variantProduct(array $variantOverrides = []): array
    {
        $category = Category::create([
            'name' => 'Honey',
            'slug' => 'honey',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pure Honey',
            'slug' => 'pure-honey',
            'price' => 80,
            'stock_quantity' => 99,
            'low_stock_alert' => 5,
            'is_active' => true,
        ]);
        $attribute = ProductAttribute::create(['name' => 'Weight', 'slug' => 'weight']);
        $value = ProductAttributeValue::create([
            'product_attribute_id' => $attribute->id,
            'value' => '1kg',
            'slug' => '1kg',
        ]);
        $variant = ProductVariant::create(array_merge([
            'product_id' => $product->id,
            'sku' => 'HONEY-1KG',
            'unit' => 'kg',
            'price_type' => 'fixed',
            'price' => 120,
            'price_adjustment' => 0,
            'stock_quantity' => 5,
            'is_default' => true,
            'is_active' => true,
        ], $variantOverrides));
        ProductVariantItem::create([
            'product_variant_id' => $variant->id,
            'product_attribute_id' => $attribute->id,
            'product_attribute_value_id' => $value->id,
        ]);

        return [$product->fresh(['variants.items.attribute', 'variants.items.value']), $variant];
    }
}
