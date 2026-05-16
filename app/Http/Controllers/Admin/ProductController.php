<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'primaryImage'])
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
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock_quantity' => 'required|integer|min:0',
        'low_stock_alert' => 'required|integer|min:0',
        'sku' => 'nullable|string|unique:products,sku',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        'primary_image_index' => 'nullable|integer',
        'image_order' => 'nullable|string'
    ]);

    $validated['slug'] = Str::slug($validated['name']);
    $validated['is_active'] = $request->has('is_active');
    $validated['is_featured'] = $request->has('is_featured');

    $product = Product::create($validated);

    // Handle images
    if ($request->hasFile('images')) {
        $images = $request->file('images');
        $primaryIndex = $request->input('primary_image_index', 0);
        $imageOrder = json_decode($request->input('image_order', '[]'), true);
        
        // Réorganiser les images si un ordre est spécifié
        if (!empty($imageOrder) && count($imageOrder) === count($images)) {
            $orderedImages = [];
            foreach ($imageOrder as $index) {
                if (isset($images[$index])) {
                    $orderedImages[] = $images[$index];
                }
            }
            $images = $orderedImages;
        }
        
        foreach ($images as $index => $image) {
            $path = $image->store('products', 'public');
            
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => $index == $primaryIndex,
                'order' => $index
            ]);
        }
    }

    return redirect()->route('admin.products.index')
        ->with('success', __('admin.product_created'));
}

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $product->load('images');
        return view('admin.products.edit', compact('product', 'categories'));
    }
    

    public function show(Product $product)
    {
        // Charger les relations de base
        $product->load(['category', 'images']);
        
        // Récupérer uniquement les orderItems NON annulés
        $orderItemsQuery = OrderItem::where('product_id', $product->id)
            ->whereHas('order', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->with(['order' => function($query) {
                $query->select('id', 'order_number', 'status', 'created_at');
            }])
            ->orderBy('created_at', 'desc');
        
        $orderItems = $orderItemsQuery->get();
        $recentOrders = $orderItemsQuery->take(10)->get();
        
        // Récupérer aussi toutes les commandes (y compris annulées) pour les statistiques globales
        $allOrderItems = OrderItem::where('product_id', $product->id)
            ->with(['order' => function($query) {
                $query->select('id', 'order_number', 'status', 'created_at');
            }])
            ->get();
        
        // Statistiques des commandes annulées
        $cancelledOrderItems = $allOrderItems->filter(function($item) {
            return $item->order && $item->order->status === 'cancelled';
        });
        
        // Calcul des statistiques - EXCLURE LES COMMANDES ANNULÉES
        $ordersData = [
            // Statistiques des commandes VALIDES (non annulées)
            'total_orders' => $orderItems->count(),
            'total_quantity' => $orderItems->sum('quantity'),
            'total_revenue' => $orderItems->sum('subtotal'),
            'average_order_value' => $orderItems->avg('subtotal'),
            'last_order_date' => $orderItems->max('created_at'),
            'recent_orders' => $recentOrders,
            
            // Statistiques des commandes ANNULÉES
            'cancelled_orders_count' => $cancelledOrderItems->count(),
            'cancelled_quantity' => $cancelledOrderItems->sum('quantity'),
            'cancelled_revenue' => $cancelledOrderItems->sum('subtotal'),
            
            // Statistiques TOTALES (tous statuts confondus)
            'total_all_orders' => $allOrderItems->count(),
            'total_all_quantity' => $allOrderItems->sum('quantity'),
            'total_all_revenue' => $allOrderItems->sum('subtotal'),
            
            // Statistiques par mois - EXCLURE LES ANNULÉES
            'monthly_stats' => OrderItem::where('product_id', $product->id)
                ->whereHas('order', function($query) {
                    $query->where('status', '!=', 'cancelled');
                })
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, 
                            SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get(),
                
            // Statistiques par statut (TOUS les statuts)
            'status_stats' => OrderItem::where('product_id', $product->id)
                ->whereHas('order')
                ->selectRaw('orders.status, COUNT(*) as order_count, SUM(order_items.quantity) as total_quantity, SUM(order_items.subtotal) as total_revenue')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->groupBy('orders.status')
                ->get(),
                
            // Calculs supplémentaires
            'avg_quantity_per_order' => $orderItems->count() > 0 
                ? round($orderItems->sum('quantity') / $orderItems->count(), 2)
                : 0,
                
            'activity_period' => $orderItems->isNotEmpty() 
                ? $orderItems->min('created_at')->format('d/m/Y') . ' - ' . $orderItems->max('created_at')->format('d/m/Y')
                : 'Aucune commande valide',
                
            // Taux d'annulation
            'cancellation_rate' => $allOrderItems->count() > 0 
                ? round(($cancelledOrderItems->count() / $allOrderItems->count()) * 100, 2)
                : 0
        ];
        
        return view('admin.products.show', compact('product', 'ordersData'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_alert' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        $product->update($validated);

        // Handle new images
        if ($request->hasFile('images')) {
            $currentImagesCount = $product->images()->count();
            
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                
                $isPrimary = false;
                // Make the first new image primary only if there are no existing images
                if ($currentImagesCount === 0 && $index === 0) {
                    $isPrimary = true;
                }
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $isPrimary,
                    'order' => $currentImagesCount + $index
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', __('admin.product_updated'));
    }

    public function destroy(Product $product)
    {
        // Delete product images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', __('admin.product_deleted'));
    }

    public function setPrimaryImage(Request $request, $imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);
            $product = $image->product;
            
            // Remove primary from all images of this product
            ProductImage::where('product_id', $product->id)
                ->update(['is_primary' => false]);
            
            // Set this image as primary
            $image->update(['is_primary' => true]);
            
            return response()->json([
                'success' => true,
                'message' => __('admin.primary_image_updated')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin.error_with_message', ['message' => $e->getMessage()])
            ], 500);
        }
    }

    public function deleteImage($imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);
            $product = $image->product;
            $isPrimary = $image->is_primary;
            
            // Delete the image from storage
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
            
            // If the deleted image was primary and there are other images,
            // make the first remaining image primary
            if ($isPrimary && $product->images()->count() > 0) { 
                $remainingImages = $product->images()->orderBy('order')->get();
                // Set the first image as primary
                $newPrimaryImage = $remainingImages->first();
                $newPrimaryImage->update(['is_primary' => true]);
                
                return response()->json([
                    'success' => true,
                    'message' => __('admin.image_deleted_new_primary'),
                    'new_primary_id' => $newPrimaryImage->id
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin.image_deleted')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin.image_delete_failed')
            ], 500);
        }
    }
}