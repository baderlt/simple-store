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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        $product = Product::create($validated);

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'order' => $index
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $product->load('images');
        return view('admin.products.edit', compact('product', 'categories'));
    }
    // app/Http/Controllers/Admin/ProductController.php

public function show(Product $product)
{
    // Charger les relations de base
    $product->load(['category', 'images']);
    
    // Récupérer uniquement les orderItems NON annulés
    $orderItemsQuery = OrderItem::where('product_id', $product->id)
        ->whereHas('order', function($query) {
            $query->where('status', '!=', 'cancelled'); // Exclure les commandes annulées
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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $currentImagesCount === 0 && $index === 0,
                    'order' => $currentImagesCount + $index
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // Delete product images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }

    public function deleteImage($id)
    {
        $image = ProductImage::findOrFail($id);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image deleted successfully');
    }
}
