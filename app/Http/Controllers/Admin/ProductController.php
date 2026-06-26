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
use App\Support\StorefrontCache;
use Illuminate\Database\QueryException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function __construct(private readonly ProductImageOptimizer $productImageOptimizer)
    {
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $products = Product::with(['category', 'primaryImage', 'variants'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('variants', function ($query) use ($search) {
                            $query->where('sku', 'like', "%{$search}%");
                        })
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->query('status') === 'active'))
            ->when($request->query('stock') === 'out', fn ($query) => $query->where('stock_quantity', 0))
            ->when($request->query('stock') === 'low', fn ($query) => $query->whereColumn('stock_quantity', '<=', 'low_stock_alert')->where('stock_quantity', '>', 0))
            ->when($request->query('stock') === 'sufficient', fn ($query) => $query->whereColumn('stock_quantity', '>', 'low_stock_alert'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

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
                $validated['slug'] = $this->uniqueSlugForProduct($validated['name']);
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

        StorefrontCache::clearHome();
        Cache::forget('admin.dashboard.v1');

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

        $validOrderItemsQuery = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', fn ($query) => $query->where('status', '!=', 'cancelled'));

        $allOrderItemsQuery = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order');

        $validStats = (clone $validOrderItemsQuery)
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('COALESCE(SUM(quantity), 0) as total_quantity')
            ->selectRaw('COALESCE(SUM(subtotal), 0) as total_revenue')
            ->selectRaw('COALESCE(AVG(subtotal), 0) as average_order_value')
            ->selectRaw('MIN(created_at) as first_order_date')
            ->selectRaw('MAX(created_at) as last_order_date')
            ->first();

        $allStats = (clone $allOrderItemsQuery)
            ->selectRaw('COUNT(*) as total_all_orders')
            ->selectRaw('COALESCE(SUM(quantity), 0) as total_all_quantity')
            ->selectRaw('COALESCE(SUM(subtotal), 0) as total_all_revenue')
            ->first();

        $cancelledStats = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', fn ($query) => $query->where('status', 'cancelled'))
            ->selectRaw('COUNT(*) as cancelled_orders_count')
            ->selectRaw('COALESCE(SUM(quantity), 0) as cancelled_quantity')
            ->selectRaw('COALESCE(SUM(subtotal), 0) as cancelled_revenue')
            ->first();

        $statusStats = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.product_id', $product->id)
            ->select('orders.status')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_quantity')
            ->selectRaw('COALESCE(SUM(order_items.subtotal), 0) as total_revenue')
            ->groupBy('orders.status')
            ->get();

        $recentOrders = (clone $validOrderItemsQuery)
            ->with(['order' => function ($query) {
                $query->select('id', 'order_number', 'status', 'created_at');
            }])
            ->latest()
            ->take(10)
            ->get();

        $totalOrders = (int) $validStats->total_orders;
        $totalAllOrders = (int) $allStats->total_all_orders;
        $firstOrderDate = $validStats->first_order_date ? Carbon::parse($validStats->first_order_date) : null;
        $lastOrderDate = $validStats->last_order_date ? Carbon::parse($validStats->last_order_date) : null;

        $ordersData = [
            'total_orders' => $totalOrders,
            'total_quantity' => (int) $validStats->total_quantity,
            'total_revenue' => (float) $validStats->total_revenue,
            'average_order_value' => (float) $validStats->average_order_value,
            'last_order_date' => $lastOrderDate,
            'recent_orders' => $recentOrders,
            'status_stats' => $statusStats,
            'cancelled_orders_count' => (int) $cancelledStats->cancelled_orders_count,
            'cancelled_quantity' => (int) $cancelledStats->cancelled_quantity,
            'cancelled_revenue' => (float) $cancelledStats->cancelled_revenue,
            'total_all_orders' => $totalAllOrders,
            'total_all_quantity' => (int) $allStats->total_all_quantity,
            'total_all_revenue' => (float) $allStats->total_all_revenue,
            'avg_quantity_per_order' => $totalOrders > 0 ? round(((int) $validStats->total_quantity) / $totalOrders, 2) : 0,
            'activity_period' => $firstOrderDate && $lastOrderDate ? $firstOrderDate->format('d/m/Y') . ' - ' . $lastOrderDate->format('d/m/Y') : 'Aucune commande valide',
            'cancellation_rate' => $totalAllOrders > 0 ? round(((int) $cancelledStats->cancelled_orders_count / $totalAllOrders) * 100, 2) : 0,
        ];

        return view('admin.products.show', compact('product', 'ordersData'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product);
        $variantsPayload = $this->validateVariantsPayload($request, (float) $validated['price']);
        $this->validateImageLimit($request, $product);

        DB::transaction(function () use ($request, $product, $validated, $variantsPayload) {
            $validated['slug'] = $this->uniqueSlugForProduct($validated['name'], $product);
            $validated['is_active'] = $request->has('is_active');
            $validated['is_featured'] = $request->has('is_featured');

            $product->update($validated);
            $this->storeProductImages($request, $product, true);
            $storedPaths = [];
            $this->syncVariants($request, $product, $variantsPayload, $storedPaths);
        });

        StorefrontCache::clearHome();
        Cache::forget('admin.dashboard.v1');

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

        StorefrontCache::clearHome();
        Cache::forget('admin.dashboard.v1');

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
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:170',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:2048',
            'og_title' => 'nullable|string|max:95',
            'og_description' => 'nullable|string|max:170',
            'og_image' => 'nullable|string|max:2048',
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
                $imagePath = $this->productImageOptimizer->store(
                    $request->file("variant_images.$key"),
                    'products/variants'
                );
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
        abort_unless($image->product()->is($product), 404);

        DB::transaction(function () use ($product, $image) {
            $product->images()
                ->whereKeyNot($image->getKey())
                ->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return response()->json(['success' => true, 'message' => __('admin.primary_image_updated')]);
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        abort_unless($image->product()->is($product), 404);

        $wasPrimary = $image->is_primary;
        $path = $image->image_path;

        DB::transaction(function () use ($product, $image, $wasPrimary) {
            $image->delete();

            if ($wasPrimary || ! $product->images()->where('is_primary', true)->exists()) {
                $product->images()->orderBy('order')->orderBy('id')->first()?->update(['is_primary' => true]);
            }
        });

        Storage::disk('public')->delete($path);

        return response()->json([
            'success' => true,
            'message' => __('admin.image_deleted'),
        ]);
    }

    private function uniqueSlugForProduct(string $name, ?Product $product = null): string
    {
        $baseSlug = Str::slug($name) ?: 'product';
        $slug = $baseSlug;
        $counter = 2;

        while (Product::where('slug', $slug)
            ->when($product, fn ($query) => $query->whereKeyNot($product->getKey()))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

}
