<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Récupérer les bannières actives groupées par position
        $activeBanners = Banner::getActiveBannersByPosition();
        
        $categories = Category::withCount('products')->get();
        
        $featuredProducts = Product::with(['category.activeDiscounts', 'primaryImage', 'activeDiscount', 'defaultVariant', 'variants.items.attribute', 'variants.items.value'])
            ->where('is_featured', true)
            ->available()
            ->limit(8)
            ->get();
            
        // Statistiques pour les compteurs animés
        $stats = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'orders' => \App\Models\Order::count() ?? 0,
            'customers' => 100,
        ];
        
        return view('home', compact(
            'categories', 
            'featuredProducts', 
            'activeBanners', 
            'stats'
        ));
    }
}