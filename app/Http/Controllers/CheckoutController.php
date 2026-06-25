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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);

        $isDirect = $request->boolean('direct');
        $directProduct = $isDirect ? session()->get('direct_checkout') : null;

        if ($directProduct) {
            // Use direct product as the only item in cart
            $cart = [
                ($directProduct['cart_key'] ?? $directProduct['id']) => $directProduct
            ];
        } else {
            // A normal cart checkout must not reuse an abandoned "buy now" item.
            session()->forget('direct_checkout');
            $isDirect = false;

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
        $minimumQuantity = $variant?->minimumOrderQuantity() ?? 1;
        if ($quantity < $minimumQuantity) {
            return redirect()->route('products.show', $product->slug)->with('error', __('checkout.minimum_quantity_required', ['quantity' => $minimumQuantity]));
        }

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
            'quantity_unit' => $variant?->quantityUnit() ?? '',
            'slug' => $product->slug,
            'has_discount' => $product->hasDiscount(),
            'is_direct' => true,
        ];
        
        session()->put('direct_checkout', $directProduct);
        
        return redirect()->route('checkout.index', ['direct' => 1]);
    }

    public function store(Request $request)
    {
        if ($request->boolean('is_laayoune_delivery')) {
            $request->merge([
                'customer_city' => 'Laâyoune',
            ]);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'is_laayoune_delivery' => 'nullable|boolean',
            'is_direct_checkout' => 'nullable|boolean',
        ]);

        // Only consume the direct item when this form came from a direct checkout page.
        $isDirect = (bool) ($validated['is_direct_checkout'] ?? false);
        $directProduct = $isDirect ? session()->get('direct_checkout') : null;
        
        if ($directProduct) {
            // Use direct product as cart
            $cart = [($directProduct['cart_key'] ?? $directProduct['id']) => $directProduct];
        } else {
            session()->forget('direct_checkout');
            $isDirect = false;

            // Normal cart checkout
            $cart = session()->get('cart', []);
            
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

                $minimumQuantity = $variant?->minimumOrderQuantity() ?? 1;
                if ($item['quantity'] < $minimumQuantity) {
                    throw new \Exception(__('checkout.minimum_quantity_for_product', [
                        'product' => $item['display_name'] ?? $product->name,
                        'quantity' => $minimumQuantity,
                    ]));
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

            // Clear cart/direct checkout session
            if ($isDirect) {
                session()->forget('direct_checkout');
            } else {
                session()->forget('cart');
            }

            DB::commit();

            $this->rememberSuccessfulOrder($order);
            $this->sendOrderNotificationsSafely($order);

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
        $order->loadMissing('items');

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
        Log::info('SMS notification prepared for order.', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'admin_phone' => $phoneNumber,
        ]);
        
        return true;
    }

    public function success($orderId)
    {
        $order = Order::with('items.variant')->findOrFail($orderId);

        if (! $this->canViewSuccessfulOrder($order)) {
            abort(404);
        }

        return view('checkout.success', compact('order'));
    }

    private function sendOrderNotificationsSafely(Order $order): void
    {
        try {
            $this->sendOrderNotifications($order);
        } catch (\Throwable $e) {
            Log::error('New order notification failed.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    private function rememberSuccessfulOrder(Order $order): void
    {
        $orderIds = session()->get('checkout_success_order_ids', []);
        $orderIds[] = $order->id;

        session()->put('checkout_success_order_ids', array_values(array_unique(array_slice($orderIds, -5))));
    }

    private function canViewSuccessfulOrder(Order $order): bool
    {
        $user = auth()->user();

        if ($user && ($user->isAdmin() || (int) $order->user_id === (int) $user->id)) {
            return true;
        }

        return in_array($order->id, session()->get('checkout_success_order_ids', []), true);
    }

}
