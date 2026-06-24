<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active',true)
            ->withCount('products')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        $products = $category->products()
            ->where("is_active",true)
            ->with(['category.activeDiscounts', 'primaryImage', 'activeDiscount', 'defaultVariant', 'variants.items.attribute', 'variants.items.value'])
            ->latest()
            ->paginate(12);

        return view('categories.show', compact('category', 'products'));
    }
}
