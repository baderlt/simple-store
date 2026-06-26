<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Support\StorefrontCache;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $homeData = Cache::remember(StorefrontCache::HOME_KEY, now()->addMinutes(5), function (): array {
            return [
                'activeBanners' => Banner::getActiveBannersByPosition(),
                'categories' => Category::where('is_active', true)
                    ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
                    ->get(),
                'featuredProducts' => Product::active()
                    ->withStorefrontRelations()
                    ->where('is_featured', true)
                    ->available()
                    ->limit(8)
                    ->get(),
                'stats' => [
                    'products' => Product::where('is_active', true)->count(),
                    'categories' => Category::where('is_active', true)->count(),
                    'orders' => Order::count(),
                    'customers' => 100,
                ],
            ];
        });

        $activeBanners = $homeData['activeBanners'];
        $categories = $homeData['categories'];
        $featuredProducts = $homeData['featuredProducts'];
        $stats = $homeData['stats'];

        return view('home', compact(
            'categories',
            'featuredProducts',
            'activeBanners',
            'stats'
        ));
    }
}
