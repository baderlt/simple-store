<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
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
        $product = Product::with(['activeDiscount', 'primaryImage', 'category.activeDiscounts', 'variants.items.attribute', 'variants.items.value'])->findOrFail($id);
        $variant = $this->resolveVariant($request, $product);

        if ($product->usesVariants() && !$variant) {
            return $this->errorResponse($request, __('cart.select_variant'));
        }

        $stock = $product->getCurrentStock($variant);
        if ($stock < 1) {
            return $this->errorResponse($request, __('cart.out_of_stock'));
        }

        $cart = session()->get('cart', []);
        $key = $this->cartKey($product->id, $variant?->id);

        if (isset($cart[$key])) {
            if ($cart[$key]['quantity'] >= $stock) {
                return $this->errorResponse($request, __('cart.max_quantity_reached'));
            }
            $cart[$key]['quantity']++;
        } else {
            $cart[$key] = $this->buildCartItem($product, $variant, 1);
        }

        session()->put('cart', $cart);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('cart.product_added_named', ['product' => $cart[$key]['display_name'] ?? $product->name]),
                'cart_count' => $cartCount,
                'cart_total' => $this->calculateCartTotal($cart),
                'product' => ['id' => $product->id, 'name' => $product->name, 'quantity' => $cart[$key]['quantity']]
            ]);
        }

        return back()->with('success', __('cart.product_added'));
    }

    public function update(Request $request, $id)
    {
        return $this->updateCartItem($request, $id, false);
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('cart.product_removed'),
                    'cart_count' => array_sum(array_column($cart, 'quantity')),
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
            return response()->json(['success' => true, 'message' => __('cart.cleared'), 'cart_count' => 0, 'cart_total' => 0]);
        }

        return back()->with('success', __('cart.cleared'));
    }

    public function ajaxUpdate(Request $request, $id)
    {
        return $this->updateCartItem($request, $id, true);
    }

    public function ajaxRemove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $productName = $cart[$id]['display_name'] ?? $cart[$id]['name'];
            unset($cart[$id]);
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => __('cart.product_removed_named', ['product' => $productName]),
                'cart_count' => array_sum(array_column($cart, 'quantity')),
                'cart_total' => number_format($this->calculateCartTotal($cart), 2),
                'items_count' => count($cart)
            ]);
        }

        return response()->json(['success' => false, 'message' => __('cart.product_not_found')], 404);
    }

    public function getAjax(Request $request)
    {
        $cart = session()->get('cart', []);

        if ($request->ajax() || $request->wantsJson()) {
            $items = [];
            $total = 0;

            foreach ($cart as $key => $item) {
                $itemTotal = $this->itemPrice($item) * $item['quantity'];
                $items[] = array_merge($item, ['key' => $key, 'item_total' => $itemTotal]);
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

    private function updateCartItem(Request $request, string $key, bool $json)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$key])) {
            return $json ? response()->json(['success' => false, 'message' => __('cart.product_not_found')], 404) : back()->with('error', __('cart.product_not_found'));
        }

        $quantity = max(1, (int) $request->quantity);
        $item = $cart[$key];
        $product = Product::with(['variants'])->find($item['id']);
        $variant = !empty($item['variant_id']) && $product ? ProductVariant::where('product_id', $product->id)->find($item['variant_id']) : null;
        $stock = $product ? $product->getCurrentStock($variant) : 0;

        if ($quantity > $stock) {
            $message = __('cart.max_quantity_available', ['stock' => $stock]);
            return $json ? response()->json(['success' => false, 'message' => $message], 400) : back()->with('error', $message);
        }

        $cart[$key]['quantity'] = $quantity;
        session()->put('cart', $cart);
        $itemTotal = $this->itemPrice($cart[$key]) * $quantity;

        if ($json || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('cart.quantity_updated'),
                'cart_count' => array_sum(array_column($cart, 'quantity')),
                'item_total' => number_format($itemTotal, 2),
                'cart_total' => number_format($this->calculateCartTotal($cart), 2),
                'quantity' => $quantity
            ]);
        }

        return back()->with('success', __('cart.updated'));
    }

    private function resolveVariant(Request $request, Product $product): ?ProductVariant
    {
        $variantId = $request->input('variant_id');
        if (!$variantId) {
            return null;
        }

        return $product->variants->firstWhere('id', (int) $variantId);
    }

    private function buildCartItem(Product $product, ?ProductVariant $variant, int $quantity): array
    {
        $basePrice = $product->getCurrentPrice($variant);
        $finalPrice = $product->getDiscountedPrice($basePrice);
        $attributes = $variant ? $variant->option_values : [];
        $variantLabel = $attributes ? implode(' / ', array_values($attributes)) : null;

        return [
            'id' => $product->id,
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
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
        ];
    }

    private function cartKey(int $productId, ?int $variantId): string
    {
        return $variantId ? "product_{$productId}_variant_{$variantId}" : (string) $productId;
    }

    private function calculateCartTotal(array $cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $this->itemPrice($item) * $item['quantity'];
        }
        return $total;
    }

    private function itemPrice(array $item): float
    {
        return (float) ($item['has_discount'] ? $item['final_price'] : $item['price']);
    }

    private function errorResponse(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 400);
        }
        return back()->with('error', $message);
    }
}
