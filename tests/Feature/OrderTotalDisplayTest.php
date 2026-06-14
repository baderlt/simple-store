<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTotalDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_success_page_does_not_add_delivery_fee_twice(): void
    {
        $order = $this->createOrder();

        $this->get(route('order.success', $order))
            ->assertOk()
            ->assertSee('100.00 DH')
            ->assertSee('30.00 DH')
            ->assertSee('130.00 DH')
            ->assertDontSee('160.00 DH');
    }

    public function test_customer_and_admin_order_details_use_the_stored_total(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->createOrder(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('130.00 DH')
            ->assertDontSee('160.00 DH');

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('130.00 DH')
            ->assertDontSee('160.00 DH');

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('130.00 DH')
            ->assertDontSee('160.00 DH');
    }

    private function createOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'order_number' => 'ORD-TOTAL-TEST',
            'customer_name' => 'Total Test',
            'customer_phone' => '0600000000',
            'customer_address' => 'Test address',
            'customer_city' => 'Casablanca',
            'subtotal' => 100,
            'discount_amount' => 0,
            'delivery_fee' => 30,
            'total' => 130,
            'status' => 'pending',
            'payment_method' => 'cash_on_delivery',
        ], $overrides));
    }
}
