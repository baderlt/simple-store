<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockLog;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $orders = Order::with(['items.product', 'items.variant'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('customer_address', 'like', "%{$search}%")
                        ->orWhere('customer_city', 'like', "%{$search}%")
                        ->orWhereHas('items.product', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('date_to')))
            ->when($request->filled('min_total'), fn ($query) => $query->where('total', '>=', $request->query('min_total')))
            ->when($request->filled('max_total'), fn ($query) => $query->where('total', '<=', $request->query('max_total')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'items.variant', 'user']);
        return view('admin.orders.show', compact('order'));
    }

public function updateStatus(Request $request, Order $order)
{
    $validated = $request->validate([
        'status' => 'required|in:pending,preparing,out_for_delivery,delivered,cancelled'
    ]);
    
    $oldStatus = $order->status;
    $newStatus = $request->status;
    
    // Si on annule la commande, restaurer le stock
    if($newStatus == 'cancelled' && $oldStatus != 'cancelled') {
        foreach($order->items as $item) {
            $product = Product::find($item->product_id);
            $variant = $item->product_variant_id ? ProductVariant::find($item->product_variant_id) : null;

            if($product) {
                if ($variant) {
                    $variant->increment('stock_quantity', $item->quantity);
                    $quantityAfter = $variant->fresh()->stock_quantity;
                } else {
                    $product->increment('stock_quantity', $item->quantity);
                    $quantityAfter = $product->fresh()->stock_quantity;
                }

                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'quantity_change' => $item->quantity,
                    'quantity_after' => $quantityAfter,
                    'type' => 'return',
                    'notes' => "Commande annulée #{$order->order_number}"
                ]);
            }
        }
    }
    // Si on réactive une commande annulée, déduire le stock
    else if($oldStatus == 'cancelled' && $newStatus != 'cancelled') {
        foreach($order->items as $item) {
            $product = Product::find($item->product_id);
            $variant = $item->product_variant_id ? ProductVariant::find($item->product_variant_id) : null;
            $stock = $variant ? $variant->stock_quantity : ($product?->stock_quantity ?? 0);

            if($product && $stock >= $item->quantity) {
                if ($variant) {
                    $variant->decrement('stock_quantity', $item->quantity);
                    $quantityAfter = $variant->fresh()->stock_quantity;
                } else {
                    $product->decrement('stock_quantity', $item->quantity);
                    $quantityAfter = $product->fresh()->stock_quantity;
                }

                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'quantity_change' => -$item->quantity,
                    'quantity_after' => $quantityAfter,
                    'type' => 'sale',
                    'notes' => "Commande réactivée #{$order->order_number}"
                ]);
            }
        }
    }
    
    $order->update($validated);

    return back()->with('success', __('admin.order_status_updated'));
}

    public function invoice(Order $order)
    {
        $order->load(['items.product', 'items.variant']);
        return view('admin.orders.invoice', compact('order'));
    }
}
