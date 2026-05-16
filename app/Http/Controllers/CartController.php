<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::with(['activeDiscount', 'primaryImage', 'category.activeDiscounts'])->findOrFail($id);
        
        if ($product->stock_quantity < 1) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('cart.out_of_stock')
                ], 400);
            }
            return back()->with('error', __('cart.out_of_stock'));
        }

        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            if ($cart[$id]['quantity'] >= $product->stock_quantity) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('cart.max_quantity_reached')
                    ], 400);
                }
                return back()->with('error', __('cart.max_quantity_reached'));
            }
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'final_price' => (float) $product->final_price,
                'image' => $product->primaryImage ? $product->primaryImage->image_path : null,
                'quantity' => 1,
                'slug' => $product->slug,
                'has_discount' => $product->hasDiscount(),
            ];
        }

        session()->put('cart', $cart);
        
        // Calculate total items in cart
        $cartCount = array_sum(array_column($cart, 'quantity'));
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('cart.product_added_named', ['product' => $product->name]),
                'cart_count' => $cartCount,
                'cart_total' => $this->calculateCartTotal($cart),
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $cart[$id]['quantity']
                ]
            ]);
        }
        
        return back()->with('success', __('cart.product_added'));
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            $quantity = max(1, (int) $request->quantity);
            
            // Check stock
            $product = Product::find($id);
            if ($product && $quantity > $product->stock_quantity) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('cart.quantity_unavailable')
                    ], 400);
                }
                return back()->with('error', __('cart.quantity_unavailable'));
            }
            
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
            
            if ($request->ajax() || $request->wantsJson()) {
                $cartCount = array_sum(array_column($cart, 'quantity'));
                return response()->json([
                    'success' => true,
                    'message' => __('cart.quantity_updated'),
                    'cart_count' => $cartCount,
                    'cart_total' => $this->calculateCartTotal($cart),
                    'item_total' => $cart[$id]['quantity'] * ($cart[$id]['has_discount'] ? $cart[$id]['final_price'] : $cart[$id]['price'])
                ]);
            }
        }

        return back()->with('success', __('cart.updated'));
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            
            if (request()->ajax() || request()->wantsJson()) {
                $cartCount = array_sum(array_column($cart, 'quantity'));
                return response()->json([
                    'success' => true,
                    'message' => __('cart.product_removed'),
                    'cart_count' => $cartCount,
                    'cart_total' => $this->calculateCartTotal($cart)
                ]);
            }
        }

        return back()->with('success', __('cart.product_removed'));
    }

    public function clear()
    {
        session()->forget('cart');
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('cart.cleared'),
                'cart_count' => 0,
                'cart_total' => 0
            ]);
        }
        
        return back()->with('success', __('cart.cleared'));
    }
    
    /**
     * Calculate total price of cart
     */
    private function calculateCartTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $price = $item['has_discount'] ? $item['final_price'] : $item['price'];
            $total += $price * $item['quantity'];
        }
        return $total;
    }
// Add these methods to your CartController

public function ajaxUpdate(Request $request, $id)
{
    $cart = session()->get('cart', []);
    
    if (isset($cart[$id])) {
        $quantity = max(1, (int) $request->quantity);
        
        // Check stock
        $product = Product::find($id);
        if ($product && $quantity > $product->stock_quantity) {
            return response()->json([
                'success' => false,
                'message' => __('cart.max_quantity_available', ['stock' => $product->stock_quantity])
            ], 400);
        }
        
        $cart[$id]['quantity'] = $quantity;
        session()->put('cart', $cart);
        
        // Calculate cart totals
        $cartCount = array_sum(array_column($cart, 'quantity'));
        $itemTotal = $cart[$id]['quantity'] * ($cart[$id]['has_discount'] ? $cart[$id]['final_price'] : $cart[$id]['price']);
        
        // Calculate cart total
        $cartTotal = 0;
        foreach ($cart as $item) {
            $itemPrice = $item['has_discount'] ? $item['final_price'] : $item['price'];
            $cartTotal += $itemPrice * $item['quantity'];
        }
        
        return response()->json([
            'success' => true,
            'message' => __('cart.quantity_updated'),
            'cart_count' => $cartCount,
            'item_total' => number_format($itemTotal, 2),
            'cart_total' => number_format($cartTotal, 2),
            'quantity' => $quantity
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => __('cart.product_not_found')
    ], 404);
}

public function ajaxRemove($id)
{
    $cart = session()->get('cart', []);
    
    if (isset($cart[$id])) {
        $productName = $cart[$id]['name'];
        unset($cart[$id]);
        session()->put('cart', $cart);
        
        // Calculate cart total
        $cartTotal = 0;
        $cartCount = 0;
        foreach ($cart as $item) {
            $itemPrice = $item['has_discount'] ? $item['final_price'] : $item['price'];
            $cartTotal += $itemPrice * $item['quantity'];
            $cartCount += $item['quantity'];
        }
        
        return response()->json([
            'success' => true,
            'message' => __('cart.product_removed_named', ['product' => $productName]),
            'cart_count' => $cartCount,
            'cart_total' => number_format($cartTotal, 2),
            'items_count' => count($cart)
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => __('cart.product_not_found')
    ], 404);
}
public function getAjax(Request $request)
{
    $cart = session()->get('cart', []);
    
    if ($request->ajax() || $request->wantsJson()) {
        $items = [];
        $total = 0;
        
        foreach ($cart as $item) {
            $itemTotal = $item['has_discount'] ? 
                ($item['final_price'] * $item['quantity']) : 
                ($item['price'] * $item['quantity']);
            
            $items[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'final_price' => $item['final_price'],
                'image' => $item['image'],
                'quantity' => $item['quantity'],
                'slug' => $item['slug'],
                'has_discount' => $item['has_discount'],
                'item_total' => $itemTotal
            ];
            
            $total += $itemTotal;
        }
        
        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => $total,
            'count' => array_sum(array_column($cart, 'quantity')),
            'item_count' => count($cart)
        ]);
    }
    
    return abort(404);
}
}