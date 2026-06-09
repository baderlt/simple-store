<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'primaryImage', 'variants'])
            ->latest()
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        DB::transaction(function () use ($request, $validated) {
            $validated['slug'] = Str::slug($validated['name']);
            $validated['is_active'] = $request->has('is_active');
            $validated['is_featured'] = $request->has('is_featured');

            $product = Product::create($validated);
            $this->storeProductImages($request, $product);
            $this->syncVariants($request, $product);
        });

        return redirect()->route('admin.products.index')
            ->with('success', __('admin.product_created'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $product->load(['images', 'variants.items.attribute', 'variants.items.value']);
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'variants.items.attribute', 'variants.items.value']);

        $orderItemsQuery = OrderItem::where('product_id', $product->id)
            ->whereHas('order', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->with(['order' => function($query) {
                $query->select('id', 'order_number', 'status', 'created_at');
            }])
            ->orderBy('created_at', 'desc');

        $orderItems = $orderItemsQuery->get();
        $recentOrders = (clone $orderItemsQuery)->take(10)->get();

        $allOrderItems = OrderItem::where('product_id', $product->id)
            ->with(['order' => function($query) {
                $query->select('id', 'order_number', 'status', 'created_at');
            }])
            ->get();

        $cancelledOrderItems = $allOrderItems->filter(fn ($item) => $item->order && $item->order->status === 'cancelled');

        $ordersData = [
            'total_orders' => $orderItems->count(),
            'total_quantity' => $orderItems->sum('quantity'),
            'total_revenue' => $orderItems->sum('subtotal'),
            'average_order_value' => $orderItems->avg('subtotal'),
            'last_order_date' => $orderItems->max('created_at'),
            'recent_orders' => $recentOrders,
            'cancelled_orders_count' => $cancelledOrderItems->count(),
            'cancelled_quantity' => $cancelledOrderItems->sum('quantity'),
            'cancelled_revenue' => $cancelledOrderItems->sum('subtotal'),
            'total_all_orders' => $allOrderItems->count(),
            'total_all_quantity' => $allOrderItems->sum('quantity'),
            'total_all_revenue' => $allOrderItems->sum('subtotal'),
            'monthly_stats' => OrderItem::where('product_id', $product->id)
                ->whereHas('order', fn ($query) => $query->where('status', '!=', 'cancelled'))
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get(),
            'status_stats' => OrderItem::where('product_id', $product->id)
                ->whereHas('order')
                ->selectRaw('orders.status, COUNT(*) as order_count, SUM(order_items.quantity) as total_quantity, SUM(order_items.subtotal) as total_revenue')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->groupBy('orders.status')
                ->get(),
            'avg_quantity_per_order' => $orderItems->count() > 0 ? round($orderItems->sum('quantity') / $orderItems->count(), 2) : 0,
            'activity_period' => $orderItems->isNotEmpty() ? $orderItems->min('created_at')->format('d/m/Y') . ' - ' . $orderItems->max('created_at')->format('d/m/Y') : 'Aucune commande valide',
            'cancellation_rate' => $allOrderItems->count() > 0 ? round(($cancelledOrderItems->count() / $allOrderItems->count()) * 100, 2) : 0
        ];

        return view('admin.products.show', compact('product', 'ordersData'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product);

        DB::transaction(function () use ($request, $product, $validated) {
            $validated['slug'] = Str::slug($validated['name']);
            $validated['is_active'] = $request->has('is_active');
            $validated['is_featured'] = $request->has('is_featured');

            $product->update($validated);
            $this->storeProductImages($request, $product, true);
            $this->syncVariants($request, $product);
        });

        return redirect()->route('admin.products.index')
            ->with('success', __('admin.product_updated'));
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        foreach ($product->variants as $variant) {
            if ($variant->image_path) {
                Storage::disk('public')->delete($variant->image_path);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', __('admin.product_deleted'));
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $productId = $product?->id;

        return $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_alert' => 'required|integer|min:0',
            'sku' => 'nullable|string|' . ($productId ? 'unique:products,sku,' . $productId : 'unique:products,sku'),
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'has_variants' => 'nullable|boolean',
            'variants_payload' => 'nullable|string',
            'variant_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'primary_image_index' => 'nullable|integer',
            'image_order' => 'nullable|string'
        ]);
    }

    private function storeProductImages(Request $request, Product $product, bool $appendOnly = false): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

        $images = $request->file('images');
        $primaryIndex = (int) $request->input('primary_image_index', 0);
        $imageOrder = json_decode($request->input('image_order', '[]'), true);

        if (!$appendOnly && !empty($imageOrder) && count($imageOrder) === count($images)) {
            $orderedImages = [];
            foreach ($imageOrder as $index) {
                if (isset($images[$index])) {
                    $orderedImages[] = $images[$index];
                }
            }
            $images = $orderedImages;
        }

        $currentImagesCount = $product->images()->count();

        foreach ($images as $index => $image) {
            $path = $image->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => $appendOnly ? ($currentImagesCount === 0 && $index === 0) : ($index === $primaryIndex),
                'order' => $currentImagesCount + $index,
            ]);
        }
    }

    private function syncVariants(Request $request, Product $product): void
    {
        if (!$request->boolean('has_variants')) {
            $this->deleteVariants($product, $product->variants()->pluck('id')->all());
            return;
        }

        $payload = json_decode($request->input('variants_payload', ''), true);
        if (!is_array($payload) || empty($payload['variants'])) {
            throw ValidationException::withMessages(['variants_payload' => __('admin.variant_required')]);
        }

        $seenCombinations = [];
        $seenSkus = [];
        $defaultCount = 0;
        $keptVariantIds = [];
        $activeVariantIds = [];

        foreach ($payload['variants'] as $variantData) {
            $values = $variantData['values'] ?? [];
            if (empty($values) || !is_array($values)) {
                throw ValidationException::withMessages(['variants_payload' => __('admin.variant_missing_attributes')]);
            }

            ksort($values);
            $signature = collect($values)->map(fn ($value, $attribute) => Str::slug($attribute) . ':' . Str::slug($value))->implode('|');
            if (isset($seenCombinations[$signature])) {
                throw ValidationException::withMessages(['variants_payload' => __('admin.variant_duplicate')]);
            }
            $seenCombinations[$signature] = true;

            $skuSignature = trim((string) ($variantData['sku'] ?? ''));
            if ($skuSignature !== '') {
                if (isset($seenSkus[$skuSignature])) {
                    throw ValidationException::withMessages(['variants_payload' => __('admin.variant_duplicate_sku')]);
                }
                $seenSkus[$skuSignature] = true;
            }

            $isDefault = !empty($variantData['is_default']);
            $defaultCount += $isDefault ? 1 : 0;

            $variantId = !empty($variantData['id']) ? (int) $variantData['id'] : null;
            $variant = $variantId ? $product->variants()->whereKey($variantId)->first() : null;

            $key = $variantData['key'] ?? (string) $variantId;
            $imagePath = $variant?->image_path;
            if ($key && $request->hasFile("variant_images.$key")) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file("variant_images.$key")->store('products/variants', 'public');
            }

            $data = [
                'sku' => !empty($variantData['sku']) ? trim($variantData['sku']) : null,
                'unit' => !empty($variantData['unit']) ? trim($variantData['unit']) : null,
                'price_type' => ($variantData['price_type'] ?? 'fixed') === 'adjustment' ? 'adjustment' : 'fixed',
                'price_adjustment' => (float) ($variantData['price_adjustment'] ?? 0),
                'price' => ($variantData['price_type'] ?? 'fixed') === 'adjustment'
                    ? max(0, (float) $product->price + (float) ($variantData['price_adjustment'] ?? 0))
                    : (float) $variantData['price'],
                'stock_quantity' => (int) $variantData['stock_quantity'],
                'image_path' => $imagePath,
                'is_default' => $isDefault,
                'is_active' => array_key_exists('is_active', $variantData) ? (bool) $variantData['is_active'] : true,
            ];

            if ($data['price'] < 0 || $data['stock_quantity'] < 0 || !in_array($data['price_type'], ['fixed', 'adjustment'], true)) {
                throw ValidationException::withMessages(['variants_payload' => __('admin.variant_invalid_values')]);
            }

            $variant = $variant ?: new ProductVariant(['product_id' => $product->id]);
            $variant->fill($data);
            $variant->product_id = $product->id;
            $variant->save();
            $keptVariantIds[] = $variant->id;
            if ($variant->is_active) {
                $activeVariantIds[] = $variant->id;
            }

            $variant->items()->delete();
            foreach ($values as $attributeName => $valueName) {
                $attribute = ProductAttribute::firstOrCreate(
                    ['slug' => Str::slug($attributeName)],
                    ['name' => trim($attributeName)]
                );
                $value = ProductAttributeValue::firstOrCreate(
                    ['product_attribute_id' => $attribute->id, 'slug' => Str::slug($valueName)],
                    ['value' => trim($valueName)]
                );
                ProductVariantItem::create([
                    'product_variant_id' => $variant->id,
                    'product_attribute_id' => $attribute->id,
                    'product_attribute_value_id' => $value->id,
                ]);
            }
        }

        if ($defaultCount !== 1 || !collect($payload['variants'])->contains(fn ($variant) => !empty($variant['is_default']) && ($variant['is_active'] ?? true))) {
            ProductVariant::whereIn('id', $keptVariantIds)->update(['is_default' => false]);
            if (!empty($activeVariantIds)) {
                ProductVariant::whereKey($activeVariantIds[0])->update(['is_default' => true]);
            }
        }

        $deleteIds = $product->variants()->whereNotIn('id', $keptVariantIds)->pluck('id')->all();
        $this->deleteVariants($product, $deleteIds);
    }

    private function deleteVariants(Product $product, array $variantIds): void
    {
        if (empty($variantIds)) {
            return;
        }

        $variants = $product->variants()->whereIn('id', $variantIds)->get();
        foreach ($variants as $variant) {
            if ($variant->image_path) {
                Storage::disk('public')->delete($variant->image_path);
            }
            $variant->delete();
        }
    }

    public function setPrimaryImage(Request $request, $imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);
            $product = $image->product;
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
            return response()->json(['success' => true, 'message' => __('admin.primary_image_updated')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('admin.error_with_message', ['message' => $e->getMessage()])], 500);
        }
    }

    public function deleteImage($imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);
            $product = $image->product;
            $isPrimary = $image->is_primary;
            Storage::disk('public')->delete($image->image_path);
            $image->delete();

            if ($isPrimary && $product->images()->count() > 0) {
                $newPrimaryImage = $product->images()->orderBy('order')->first();
                $newPrimaryImage->update(['is_primary' => true]);
                return response()->json(['success' => true, 'message' => __('admin.image_deleted_new_primary'), 'new_primary_id' => $newPrimaryImage->id]);
            }
            return response()->json(['success' => true, 'message' => __('admin.image_deleted')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('admin.image_delete_failed')], 500);
        }
    }
}
