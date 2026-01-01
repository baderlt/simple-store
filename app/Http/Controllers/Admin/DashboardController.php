<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic Stats
        $totalSales = Order::where('status', '!=', 'cancelled')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        
        // Low stock products
        $lowStockProducts = Product::where('stock_quantity', '<=', DB::raw('low_stock_alert'))->count();
        $lowStockProductsList = Product::where('stock_quantity', '<=', DB::raw('low_stock_alert'))
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();
        
        // Today's stats
        $today = now()->format('Y-m-d');
        $todaySales = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total');
        $todayOrders = Order::whereDate('created_at', $today)->count();
        
        // Monthly stats
        $month = now()->format('Y-m');
        $monthlySales = Order::where('created_at', 'like', $month . '%')
            ->where('status', '!=', 'cancelled')
            ->sum('total');
        $monthlyOrders = Order::where('created_at', 'like', $month . '%')->count();
        
        // Average order value
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Order status counts
        $pendingOrders = Order::where('status', 'pending')->count();
        $preparingOrders = Order::where('status', 'preparing')->count();
        $deliveryOrders = Order::where('status', 'out_for_delivery')->count();
        
        // Recent orders (last 24 hours)
        $recentOrders = Order::with('items')
            ->where('created_at', '>=', now()->subDay())
            ->latest()
            ->take(8)
            ->get();
        
        // Best selling products
        $bestSellingProducts = Product::select('products.*', DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'))
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id')
            ->orderBy('total_sold', 'desc')
            ->take(6)
            ->get();
        
        // Revenue by status
        $revenueByStatus = Order::select('status', DB::raw('SUM(total) as revenue'))
            ->groupBy('status')
            ->get()
            ->pluck('revenue', 'status')
            ->toArray();
        
        // Monthly revenue for chart
        $monthlyRevenue = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('MONTH(created_at) as month_num'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders_count')
            )
            ->whereYear('created_at', date('Y'))
            ->where('status', '!=', 'cancelled')
            ->groupBy('month', 'month_num')
            ->orderBy('month_num')
            ->get();
        
        // Prepare chart data
        $revenueData = array_fill(1, 12, 0);
        $ordersData = array_fill(1, 12, 0);
        
        foreach ($monthlyRevenue as $data) {
            $monthNum = $data->month_num;
            $revenueData[$monthNum] = $data->revenue;
            $ordersData[$monthNum] = $data->orders_count;
        }
        
        // Category statistics
        $categoryStats = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();
        
        // Daily sales for the last 7 days
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return view('admin.dashboard', compact(
            'totalSales',
            'totalOrders',
            'totalProducts',
            'totalCategories',
            'lowStockProducts',
            'lowStockProductsList',
            'todaySales',
            'todayOrders',
            'monthlySales',
            'monthlyOrders',
            'avgOrderValue',
            'pendingOrders',
            'preparingOrders',
            'deliveryOrders',
            'recentOrders',
            'bestSellingProducts',
            'revenueByStatus',
            'revenueData',
            'ordersData',
            'categoryStats',
            'dailySales'
        ));
    }
}