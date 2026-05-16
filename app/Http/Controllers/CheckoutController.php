<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        // Check if we're doing direct checkout
        $directProduct = session()->get('direct_checkout');
        
        if ($directProduct) {
            // Use direct product as the only item in cart
            $cart = [
                $directProduct['id'] => $directProduct
            ];
            $isDirect = true;
        } else {
            // Normal cart checkout
            if (empty($cart)) {
                return redirect()->route('cart.index')->with('error', __('checkout.empty_cart'));
            }
            $isDirect = false;
        }
        
        $deliveryFee = settings('delivery_fee', 30);
        
        return view('checkout.index', compact('cart', 'deliveryFee', 'isDirect'));
    }

    public function direct(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $product = Product::with(['activeDiscount', 'primaryImage'])->findOrFail($id);
        
        if ($product->stock_quantity < 1) {
            return redirect()->route('products.show', $product->slug)
                ->with('error', __('checkout.out_of_stock'));
        }
        
        $quantity = $request->input('quantity');
        
        if ($quantity > $product->stock_quantity) {
            return redirect()->route('products.show', $product->slug)
                ->with('error', __('checkout.only_units_available', ['stock' => $product->stock_quantity]));
        }
        
        // Store direct product in session
        $directProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'final_price' => (float) $product->final_price,
            'image' => $product->primaryImage ? $product->primaryImage->image_path : null,
            'quantity' => $quantity,
            'slug' => $product->slug,
            'has_discount' => $product->hasDiscount(),
            'is_direct' => true,
        ];
        
        session()->put('direct_checkout', $directProduct);
        
        return redirect()->route('checkout.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Check if we're doing direct checkout
        $directProduct = session()->get('direct_checkout');
        
        if ($directProduct) {
            // Use direct product as cart
            $cart = [$directProduct['id'] => $directProduct];
            $isDirect = true;
        } else {
            // Normal cart checkout
            $cart = session()->get('cart', []);
            $isDirect = false;
            
            if (empty($cart)) {
                return redirect()->route('cart.index')->with('error', __('checkout.empty_cart'));
            }
        }

        DB::beginTransaction();
        try {
            // Calculate totals using final_price
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['final_price'] * $item['quantity'];
            }

            $deliveryFee = settings('delivery_fee', 30);
            $total = $subtotal;

            // Create order
            $orderData = [
                'user_id' => auth()->id(),
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'customer_city' => $validated['customer_city'],
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'payment_method' => 'cash_on_delivery',
            ];
            
            // Add direct flag if it's a direct order
            if ($isDirect) {
                $orderData['is_direct'] = true;
            }

            $order = Order::create($orderData);

            // Create order items and update stock
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                
                if (!$product) {
                    throw new \Exception(__('checkout.product_not_found'));
                }
                
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception(__('checkout.insufficient_stock_for_product', ['product' => $product->name]));
                }

                // Create order item with correct prices
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'discount_price' => $product->hasDiscount() ? $product->final_price : null,
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['final_price'] * $item['quantity'],
                ]);

                // Update stock
                $product->decrement('stock_quantity', $item['quantity']);

                // Log stock change
                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'quantity_change' => -$item['quantity'],
                    'quantity_after' => $product->stock_quantity,
                    'type' => 'sale',
                    'notes' => __('checkout.stock_log_order', ['number' => $order->order_number]),
                ]);
            }

            // Send notifications
            $this->sendOrderNotifications($order);

            // Clear cart/direct checkout session
            if ($isDirect) {
                session()->forget('direct_checkout');
            } else {
                session()->forget('cart');
            }

            DB::commit();

            return redirect()->route('order.success', $order->id)
                ->with('success', __('checkout.order_placed'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Send email and SMS notifications for new order
     */
    private function sendOrderNotifications(Order $order)
    {
        // Send email notification
        $adminEmail = settings('email');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new NewOrderNotification($order));
        }

        // Send SMS notification
        $adminPhone = settings('phone');
        if ($adminPhone) {
            $this->sendSmsNotification($adminPhone, $order);
        }
    }

    /**
     * Send SMS notification to admin
     */
    private function sendSmsNotification(string $phoneNumber, Order $order)
    {
        // You'll need to integrate with an SMS gateway here
        // This is a template - replace with your actual SMS provider integration
        
        $message = "Nouvelle commande #{$order->order_number} reçue de {$order->customer_name}. Total: {$order->total} Dhs. Vérifiez votre email pour plus de détails.";
        
        // Example using Twilio (you need to install and configure it)
        /*
        try {
            $twilio = new \Twilio\Rest\Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            
            $twilio->messages->create(
                $phoneNumber,
                [
                    'from' => config('services.twilio.phone'),
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());
        }
        */
        
        // For now, just log the SMS that would be sent
        \Log::info('SMS would be sent to: ' . $phoneNumber . ' - Message: ' . $message);
        
        return true;
    }

    public function success($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);
        return view('checkout.success', compact('order'));
    }
}