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
use App\Services\ProductImageOptimizer;
use Illuminate\Database\QueryException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function __construct(private readonly ProductImageOptimizer $productImageOptimizer)
    {
    }

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
        $variantsPayload = $this->validateVariantsPayload($request, (float) $validated['price']);
        $this->validateImageLimit($request);
        $storedPaths = [];

        try {
            DB::transaction(function () use ($request, $validated, $variantsPayload, &$storedPaths) {
                $validated['slug'] = Str::slug($validated['name']);
                $validated['is_active'] = $request->has('is_active');
                $validated['is_featured'] = $request->has('is_featured');

                $product = Product::create($validated);
                $storedPaths = array_merge($storedPaths, $this->storeProductImages($request, $product));
                $this->syncVariants($request, $product, $variantsPayload, $storedPaths);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            if ($exception instanceof QueryException && Product::where('name', $validated['name'])->exists()) {
                throw ValidationException::withMessages([
                    'name' => __('admin.product_name_unique'),
                ]);
            }

            throw $exception;
        }

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
        $statusStats = $allOrderItems
            ->filter(fn ($item) => $item->order)
            ->groupBy(fn ($item) => $item->order->status)
            ->map(function ($items, $status) {
                return (object) [
                    'status' => $status,
                    'order_count' => $items->count(),
                    'total_quantity' => $items->sum('quantity'),
                    'total_revenue' => $items->sum('subtotal'),
                ];
            })
            ->values();

        $ordersData = [
            'total_orders' => $orderItems->count(),
            'total_quantity' => $orderItems->sum('quantity'),
            'total_revenue' => $orderItems->sum('subtotal'),
            'average_order_value' => $orderItems->avg('subtotal'),
            'last_order_date' => $orderItems->max('created_at'),
            'recent_orders' => $recentOrders,
            'status_stats' => $statusStats,
            'cancelled_orders_count' => $cancelledOrderItems->count(),
            'cancelled_quantity' => $cancelledOrderItems->sum('quantity'),
            'cancelled_revenue' => $cancelledOrderItems->sum('subtotal'),
            'total_all_orders' => $allOrderItems->count(),
            'total_all_quantity' => $allOrderItems->sum('quantity'),
            'total_all_revenue' => $allOrderItems->sum('subtotal'),
            'avg_quantity_per_order' => $orderItems->count() > 0 ? round($orderItems->sum('quantity') / $orderItems->count(), 2) : 0,
            'activity_period' => $orderItems->isNotEmpty() ? $orderItems->min('created_at')->format('d/m/Y') . ' - ' . $orderItems->max('created_at')->format('d/m/Y') : 'Aucune commande valide',
            'cancellation_rate' => $allOrderItems->count() > 0 ? round(($cancelledOrderItems->count() / $allOrderItems->count()) * 100, 2) : 0
        ];

        return view('admin.products.show', compact('product', 'ordersData'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product);
        $variantsPayload = $this->validateVariantsPayload($request, (float) $validated['price']);
        $this->validateImageLimit($request, $product);

        DB::transaction(function () use ($request, $product, $validated, $variantsPayload) {
            $validated['slug'] = Str::slug($validated['name']);
            $validated['is_active'] = $request->has('is_active');
            $validated['is_featured'] = $request->has('is_featured');

            $product->update($validated);
            $this->storeProductImages($request, $product, true);
            $storedPaths = [];
            $this->syncVariants($request, $product, $variantsPayload, $storedPaths);
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($productId),
            ],
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
            'primary_image_index' => 'nullable|integer|min:0',
            'new_primary_image_index' => 'nullable|integer|min:-1',
            'image_order' => 'nullable|string'
        ], [
            'name.unique' => __('admin.product_name_unique'),
        ]);
    }

    private function validateImageLimit(Request $request, ?Product $product = null): void
    {
        $newImageCount = collect($request->file('images', []))->filter()->count();
        $existingImageCount = $product?->images()->count() ?? 0;
        $maximumImages = 10;

        if ($existingImageCount + $newImageCount > $maximumImages) {
            throw ValidationException::withMessages([
                'images' => __('admin.product_images_limit', ['count' => $maximumImages]),
            ]);
        }
    }

    private function storeProductImages(Request $request, Product $product, bool $appendOnly = false): array
    {
        if (!$request->hasFile('images')) {
            return [];
        }

        $this->ensurePublicStorageLink();

        $images = collect($request->file('images'))->filter()->values();
        $existingCount = $product->images()->count();

        $primaryIndex = $appendOnly
            ? $request->integer('new_primary_image_index', -1)
            : $request->integer('primary_image_index', 0);
        $storedPaths = [];

        try {
            foreach ($images as $index => $image) {
                $path = $this->productImageOptimizer->store($image);
                $storedPaths[] = $path;
                $makePrimary = $appendOnly
                    ? ($primaryIndex === $index || ($existingCount === 0 && $index === 0 && $primaryIndex < 0))
                    : $index === $primaryIndex;

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $makePrimary,
                    'order' => $existingCount + $index,
                ]);
            }
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            report($exception);

            throw ValidationException::withMessages([
                'images' => __('admin.image_upload_failed'),
            ]);
        }

        if (!$product->images()->where('is_primary', true)->exists()) {
            $product->images()->orderBy('order')->orderBy('id')->first()?->update(['is_primary' => true]);
        }

        return $storedPaths;
    }

    private function ensurePublicStorageLink(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $link = public_path('storage');
        if (is_link($link) || file_exists($link)) {
            return;
        }

        try {
            app(Filesystem::class)->link(storage_path('app/public'), $link);
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'images' => __('admin.public_storage_unavailable'),
            ]);
        }
    }

    private function validateVariantsPayload(Request $request, float $productPrice): ?array
    {
        if (!$request->boolean('has_variants')) {
            return null;
        }

        $payload = json_decode($request->input('variants_payload', ''), true);
        if (!is_array($payload) || empty($payload['variants']) || !is_array($payload['variants'])) {
            throw ValidationException::withMessages(['variants_payload' => __('admin.variant_required')]);
        }

        $seenCombinations = [];
        $seenSkus = [];

        foreach ($payload['variants'] as $variantData) {
            if (!is_array($variantData)) {
                throw ValidationException::withMessages(['variants_payload' => __('admin.variant_invalid_values')]);
            }

            $values = $variantData['values'] ?? [];
            if (empty($values) || !is_array($values)) {
                throw ValidationException::withMessages(['variants_payload' => __('admin.variant_missing_attributes')]);
            }

            foreach ($values as $attribute => $value) {
                if (!is_string($attribute) || trim($attribute) === '' || !is_string($value) || trim($value) === '') {
                    throw ValidationException::withMessages(['variants_payload' => __('admin.variant_missing_attributes')]);
                }
            }

            ksort($values);
            $signature = collect($values)
                ->map(fn ($value, $attribute) => Str::slug($attribute) . ':' . Str::slug($value))
                ->implode('|');
            if (isset($seenCombinations[$signature])) {
                throw ValidationException::withMessages(['variants_payload' => __('admin.variant_duplicate')]);
            }
            $seenCombinations[$signature] = true;

            $sku = trim((string) ($variantData['sku'] ?? ''));
            if ($sku !== '' && isset($seenSkus[$sku])) {
                throw ValidationException::withMessages(['variants_payload' => __('admin.variant_duplicate_sku')]);
            }
            $seenSkus[$sku] = true;

            $priceType = $variantData['price_type'] ?? 'fixed';
            $priceValue = $priceType === 'adjustment'
                ? $productPrice + (float) ($variantData['price_adjustment'] ?? 0)
                : ($variantData['price'] ?? null);
            $stockQuantity = $variantData['stock_quantity'] ?? null;

            if (!in_array($priceType, ['fixed', 'adjustment'], true)
                || !is_numeric($priceValue)
                || (float) $priceValue < 0
                || !is_numeric($stockQuantity)
                || (int) $stockQuantity < 0
                || (float) $stockQuantity !== (float) (int) $stockQuantity) {
                throw ValidationException::withMessages(['variants_payload' => __('admin.variant_invalid_values')]);
            }
        }

        return $payload;
    }

    private function syncVariants(Request $request, Product $product, ?array $payload, array &$storedPaths): void
    {
        if ($payload === null) {
            $this->deleteVariants($product, $product->variants()->pluck('id')->all());
            return;
        }

        if ($request->hasFile('variant_images')) {
            $this->ensurePublicStorageLink();
        }

        $defaultCount = 0;
        $keptVariantIds = [];
        $activeVariantIds = [];

        foreach ($payload['variants'] as $variantData) {
            $values = $variantData['values'] ?? [];
            ksort($values);

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
                if (!$imagePath) {
                    throw ValidationException::withMessages([
                        'variant_images' => __('admin.image_upload_failed'),
                    ]);
                }
                $storedPaths[] = $imagePath;
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

    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        DB::transaction(function () use ($product, $image) {
            $product->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return response()->json(['success' => true, 'message' => __('admin.primary_image_updated')]);
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        $wasPrimary = $image->is_primary;
        $path = $image->image_path;

        DB::transaction(function () use ($product, $image, $wasPrimary) {
            $image->delete();

            if ($wasPrimary) {
                $product->images()->orderBy('order')->orderBy('id')->first()?->update(['is_primary' => true]);
            }
        });

        Storage::disk('public')->delete($path);

        return response()->json([
            'success' => true,
            'message' => __('admin.image_deleted'),
        ]);
    }

}
