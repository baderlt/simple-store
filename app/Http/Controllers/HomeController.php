<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\Order;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $activeBanners = Banner::getActiveBannersByPosition();

        $categories = Category::query()
            ->withCount('products')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $featuredProducts = Product::with(['category', 'primaryImage', 'activeDiscount'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->when(config('database.default') !== 'sqlite', fn ($query) => $query->where('stock_quantity', '>', 0))
            ->limit(12)
            ->get();

        $homepageSections = HomepageSection::query()
            ->where('is_enabled', true)
            ->orderBy('position')
            ->get();

        if ($homepageSections->isEmpty()) {
            $homepageSections = collect(config('storefront.homepage_sections', []))
                ->map(fn (array $section) => new HomepageSection($section + ['is_enabled' => true]));
        }

        $stats = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'orders' => Order::count(),
            'customers' => 100,
        ];

        return view('home', compact('categories', 'featuredProducts', 'activeBanners', 'homepageSections', 'stats'));
    }
}
