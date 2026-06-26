<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()
            ->withStorefrontRelations();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->query('search') . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->query('category'));
        }

        // Sort
        switch ($request->get('sort', 'newest')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('products._cards', compact('products'))->render(),
                'next_page_url' => $products->nextPageUrl(),
            ]);
        }

        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['images', 'primaryImage', 'activeDiscount', 'category.activeDiscounts', 'variants.items.attribute', 'variants.items.value'])
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->withStorefrontRelations()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function searchSuggestions(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 2 || mb_strlen($query) > 80) {
            return response()->json([]);
        }

        $likeQuery = addcslashes($query, '\\%_');

        $products = Product::active()
            ->with(['category', 'activeDiscount', 'category.activeDiscounts'])
            ->where(function ($productsQuery) use ($likeQuery) {
                $productsQuery->where('name', 'like', '%' . $likeQuery . '%')
                    ->orWhere('description', 'like', '%' . $likeQuery . '%');
            })
            ->take(10)
            ->get();

        return response()->json(
            $products->map(function (Product $product): array {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'final_price' => $product->final_price,
                    'has_discount' => $product->hasDiscount(),
                    'category_name' => $product->category?->localized_name,
                ];
            })
        );
    }
}
