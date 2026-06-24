<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $staticUrls = collect([
            ['loc' => route('home'), 'lastmod' => now(), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('products.index'), 'lastmod' => now(), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => route('categories.index'), 'lastmod' => now(), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => route('promotions.index'), 'lastmod' => now(), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => route('about'), 'lastmod' => now(), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ]);

        $categoryUrls = Category::query()
            ->where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->latest('updated_at')
            ->get()
            ->map(fn (Category $category) => [
                'loc' => route('categories.show', $category),
                'lastmod' => $category->updated_at,
                'priority' => '0.7',
                'changefreq' => 'weekly',
            ]);

        $productUrls = Product::query()
            ->where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->latest('updated_at')
            ->get()
            ->map(fn (Product $product) => [
                'loc' => route('products.show', $product->slug),
                'lastmod' => $product->updated_at,
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ]);

        $xml = view('sitemap.index', [
            'urls' => $staticUrls
                ->merge($categoryUrls)
                ->merge($productUrls)
                ->filter(fn (array $url) => filled($url['loc'])),
        ])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
