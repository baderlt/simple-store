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
                    'message' => 'Produit en rupture de stock'
                ], 400);
            }
            return back()->with('error', 'Produit en rupture de stock');
        }

        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            if ($cart[$id]['quantity'] >= $product->stock_quantity) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantité maximale atteinte'
                    ], 400);
                }
                return back()->with('error', 'Quantité maximale atteinte');
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
                'message' => $product->name . ' ajouté au panier',
                'cart_count' => $cartCount,
                'cart_total' => $this->calculateCartTotal($cart),
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $cart[$id]['quantity']
                ]
            ]);
        }
        
        return back()->with('success', 'Produit ajouté au panier');
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
                        'message' => 'Quantité demandée non disponible'
                    ], 400);
                }
                return back()->with('error', 'Quantité demandée non disponible');
            }
            
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
            
            if ($request->ajax() || $request->wantsJson()) {
                $cartCount = array_sum(array_column($cart, 'quantity'));
                return response()->json([
                    'success' => true,
                    'message' => 'Quantité mise à jour',
                    'cart_count' => $cartCount,
                    'cart_total' => $this->calculateCartTotal($cart),
                    'item_total' => $cart[$id]['quantity'] * ($cart[$id]['has_discount'] ? $cart[$id]['final_price'] : $cart[$id]['price'])
                ]);
            }
        }

        return back()->with('success', 'Panier mis à jour');
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
                    'message' => 'Produit retiré du panier',
                    'cart_count' => $cartCount,
                    'cart_total' => $this->calculateCartTotal($cart)
                ]);
            }
        }

        return back()->with('success', 'Produit retiré du panier');
    }

    public function clear()
    {
        session()->forget('cart');
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Panier vidé',
                'cart_count' => 0,
                'cart_total' => 0
            ]);
        }
        
        return back()->with('success', 'Panier vidé');
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
                'message' => 'Quantité maximale disponible : ' . $product->stock_quantity
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
            'message' => 'Quantité mise à jour',
            'cart_count' => $cartCount,
            'item_total' => number_format($itemTotal, 2),
            'cart_total' => number_format($cartTotal, 2),
            'quantity' => $quantity
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Produit non trouvé dans le panier'
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
            'message' => $productName . ' retiré du panier',
            'cart_count' => $cartCount,
            'cart_total' => number_format($cartTotal, 2),
            'items_count' => count($cart)
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Produit non trouvé dans le panier'
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