<?php

namespace Tests\Feature;

use App\Mail\NewOrderNotification;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_email_is_store_branded_and_contains_order_action(): void
    {
        Setting::set('store_name', 'Maison Dorée');

        $mail = new NewOrderNotification($this->createOrder());

        $mail->assertHasSubject('New order #ORD-EMAIL-TEST');

        $html = $mail->render();

        $this->assertStringContainsString('Une nouvelle commande vous attend', $html);
        $this->assertStringContainsString('Maison Dorée', $html);
        $this->assertStringContainsString('0600000000', $html);
        $this->assertStringContainsString('Voir et traiter la commande', $html);
    }

    private function createOrder(): Order
    {
        return Order::create([
            'order_number' => 'ORD-EMAIL-TEST',
            'customer_name' => 'Nadia',
            'customer_phone' => '0600000000',
            'customer_address' => '12 rue des Fleurs',
            'customer_city' => 'Laâyoune',
            'subtotal' => 250,
            'discount_amount' => 0,
            'delivery_fee' => 0,
            'total' => 250,
            'status' => 'pending',
            'payment_method' => 'cash_on_delivery',
        ]);
    }
}
