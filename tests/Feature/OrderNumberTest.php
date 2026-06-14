<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_first_order_number_starts_at_one(): void
    {
        $this->assertSame('ORD-0001', Order::generateOrderNumber());
    }

    public function test_it_increments_the_latest_order_number(): void
    {
        $this->createOrder('ORD-0001');
        $this->createOrder('ORD-0002');

        $this->assertSame('ORD-0003', Order::generateOrderNumber());
    }

    public function test_it_expands_beyond_four_digits(): void
    {
        $this->createOrder('ORD-283838');

        $this->assertSame('ORD-283839', Order::generateOrderNumber());
    }

    public function test_it_ignores_order_numbers_from_the_old_format(): void
    {
        $this->createOrder('ORD-20260614-DECA98');
        $this->createOrder('ORD-0010');

        $this->assertSame('ORD-0011', Order::generateOrderNumber());
    }

    private function createOrder(string $orderNumber): Order
    {
        return Order::create([
            'order_number' => $orderNumber,
            'customer_name' => 'Test Customer',
            'customer_phone' => '0600000000',
            'customer_address' => 'Test Address',
            'customer_city' => 'Test City',
            'subtotal' => 100,
            'discount_amount' => 0,
            'delivery_fee' => 0,
            'total' => 100,
            'status' => 'pending',
            'payment_method' => 'cash_on_delivery',
        ]);
    }
}
