<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items.product')->latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');
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
            
            if($product) {
                // Restaurer le stock
                $product->increment('stock_quantity', $item->quantity);
                
                // Log dans StockLog
                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'quantity_change' => $item->quantity,
                    'quantity_after' => $product->stock_quantity,
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
            
            if($product && $product->stock_quantity >= $item->quantity) {
                // Déduire le stock
                $product->decrement('stock_quantity', $item->quantity);
                
                // Log dans StockLog
                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'quantity_change' => -$item->quantity,
                    'quantity_after' => $product->stock_quantity,
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
        $order->load('items.product');
        return view('admin.orders.invoice', compact('order'));
    }
}
