<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\ProductVariant;
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
                ($directProduct['cart_key'] ?? $directProduct['id']) => $directProduct
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
            'variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        $product = Product::with(['activeDiscount', 'primaryImage', 'category.activeDiscounts', 'variants.items.attribute', 'variants.items.value'])->findOrFail($id);
        $variant = $request->filled('variant_id') ? $product->variants->first(fn ($variant) => $variant->id === (int) $request->variant_id && $variant->is_active) : null;

        if ($product->usesVariants() && !$variant) {
            return redirect()->route('products.show', $product->slug)->with('error', __('checkout.select_variant'));
        }

        $stock = $product->getCurrentStock($variant);
        if ($stock < 1) {
            return redirect()->route('products.show', $product->slug)->with('error', __('checkout.out_of_stock'));
        }

        $quantity = (int) $request->input('quantity');
        if ($quantity > $stock) {
            return redirect()->route('products.show', $product->slug)->with('error', __('checkout.only_units_available', ['stock' => $stock]));
        }

        $basePrice = $product->getCurrentPrice($variant);
        $finalPrice = $product->getDiscountedPrice($basePrice);
        $attributes = $variant ? $variant->option_values : [];
        $variantLabel = $attributes ? implode(' / ', array_values($attributes)) : null;

        $directProduct = [
            'id' => $product->id,
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'cart_key' => $variant ? "product_{$product->id}_variant_{$variant->id}" : (string) $product->id,
            'name' => $product->name,
            'display_name' => $variantLabel ? $product->name . ' - ' . $variantLabel : $product->name,
            'variant_label' => $variantLabel,
            'selected_attributes' => $attributes,
            'sku' => $variant?->sku ?: $product->sku,
            'price' => (float) $basePrice,
            'final_price' => (float) $finalPrice,
            'image' => $variant?->image_path ?: ($product->primaryImage ? $product->primaryImage->image_path : null),
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
            'is_laayoune_delivery' => 'nullable|boolean',
        ]);

        // Check if we're doing direct checkout
        $directProduct = session()->get('direct_checkout');
        
        if ($directProduct) {
            // Use direct product as cart
            $cart = [($directProduct['cart_key'] ?? $directProduct['id']) => $directProduct];
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
            // Re-resolve products, variants, prices, and stock inside the transaction.
            // Session prices are display-only and must never be trusted at order time.
            $subtotal = 0;
            $resolvedItems = [];
            foreach ($cart as $item) {
                $product = Product::with(['category.activeDiscounts', 'activeDiscount', 'variants'])
                    ->lockForUpdate()
                    ->find($item['id']);

                if (!$product) {
                    throw new \Exception(__('checkout.product_not_found'));
                }

                $variant = !empty($item['variant_id'])
                    ? ProductVariant::where('product_id', $product->id)->where('is_active', true)->lockForUpdate()->find($item['variant_id'])
                    : null;

                if ($product->usesVariants() && !$variant) {
                    throw new \Exception(__('checkout.invalid_variant'));
                }

                if ($product->getCurrentStock($variant) < $item['quantity']) {
                    throw new \Exception(__('checkout.insufficient_stock_for_product', ['product' => $item['display_name'] ?? $product->name]));
                }

                $basePrice = $product->getCurrentPrice($variant);
                $finalPrice = $product->getDiscountedPrice($basePrice);
                $subtotal += $finalPrice * $item['quantity'];
                $resolvedItems[] = compact('item', 'product', 'variant', 'basePrice', 'finalPrice');
            }

            $configuredDeliveryFee = (float) settings('delivery_fee', 30);
            $freeDeliveryThreshold = (float) settings('free_delivery_threshold', 0);
            $hasFreeCityDelivery = (bool) ($validated['is_laayoune_delivery'] ?? false);
            $deliveryFee = $hasFreeCityDelivery || ($freeDeliveryThreshold > 0 && $subtotal > $freeDeliveryThreshold)
                ? 0
                : $configuredDeliveryFee;
            $total = $subtotal + $deliveryFee;

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

            // Create order items and update the exact selected stock record.
            $purchasedProductIds = [];
            foreach ($resolvedItems as $resolved) {
                ['item' => $item, 'product' => $product, 'variant' => $variant, 'basePrice' => $basePrice, 'finalPrice' => $finalPrice] = $resolved;
                $purchasedProductIds[$product->id] = true;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_snapshot' => $variant?->option_values ?: null,
                    'price' => $basePrice,
                    'discount_price' => $product->hasDiscount() ? $finalPrice : null,
                    'quantity' => $item['quantity'],
                    'subtotal' => $finalPrice * $item['quantity'],
                ]);

                if ($variant) {
                    $variant->decrement('stock_quantity', $item['quantity']);
                    $quantityAfter = $variant->fresh()->stock_quantity;
                } else {
                    $product->decrement('stock_quantity', $item['quantity']);
                    $quantityAfter = $product->fresh()->stock_quantity;
                }

                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'quantity_change' => -$item['quantity'],
                    'quantity_after' => $quantityAfter,
                    'type' => 'sale',
                    'notes' => __('checkout.stock_log_order', ['number' => $order->order_number]) . ($variant ? ' - ' . ($item['variant_label'] ?? '') : ''),
                ]);
            }

            // Count one additional sale per distinct product in this order, regardless of quantity or variant lines.
            Product::whereKey(array_keys($purchasedProductIds))->increment('sales_count');

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
        $order = Order::with('items.variant')->findOrFail($orderId);
        return view('checkout.success', compact('order'));
    }

}
