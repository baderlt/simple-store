<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminHeaderNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_header_notifications_only_show_pending_orders(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $pendingOrder = $this->createOrder('pending', 'ORD-PENDING');
        $deliveredOrder = $this->createOrder('delivered', 'ORD-DELIVERED');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee($pendingOrder->order_number);
        $response->assertDontSee($deliveredOrder->order_number);
        $response->assertSee(__('pending_orders_only'));
    }

    public function test_arabic_admin_header_keeps_rtl_mobile_sidebar_and_notifications(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this
            ->actingAs($admin)
            ->withSession(['locale' => 'ar'])
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('right: 0;', false);
        $response->assertSee('إشعارات الطلبات الجديدة');
    }

    private function createOrder(string $status, string $orderNumber): Order
    {
        return Order::create([
            'order_number' => $orderNumber,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0600000000',
            'customer_address' => 'Test address',
            'customer_city' => 'Test city',
            'subtotal' => 100,
            'discount_amount' => 0,
            'delivery_fee' => 0,
            'total' => 100,
            'status' => $status,
            'payment_method' => 'cash_on_delivery',
        ]);
    }
}
