<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index()
    {
        $productsWithDiscount = $this->discountedProductsQuery()
            ->latest()
            ->paginate(12);

        $featuredProducts = $this->discountedProductsQuery()
            ->select('products.*')
            ->selectSub($this->activeProductDiscountPercentageSubquery(), 'product_active_discount_percentage')
            ->selectSub($this->activeCategoryDiscountPercentageSubquery(), 'category_active_discount_percentage')
            ->orderByRaw('CASE WHEN COALESCE(product_active_discount_percentage, 0) >= COALESCE(category_active_discount_percentage, 0) THEN COALESCE(product_active_discount_percentage, 0) ELSE COALESCE(category_active_discount_percentage, 0) END DESC')
            ->orderByDesc('products.created_at')
            ->take(4)
            ->get();

        $categoriesWithDiscounts = Category::whereHas('products', function (Builder $query) {
            $this->applyDiscountedProductFilters($query);
        })
            ->withCount(['products' => function (Builder $query) {
                $this->applyDiscountedProductFilters($query);
            }])
            ->having('products_count', '>', 0)
            ->get();

        $categories = Category::whereHas('products', function (Builder $query) {
            $this->applyDiscountedProductFilters($query);
        })->get();

        $maxDiscount = Discount::active()->max('discount_percentage');

        return view('promotions.index', compact(
            'productsWithDiscount',
            'featuredProducts',
            'categoriesWithDiscounts',
            'categories',
            'maxDiscount'
        ));
    }

    private function discountedProductsQuery(): Builder
    {
        return Product::active()
            ->available()
            ->withAnyActiveDiscount()
            ->withStorefrontRelations();
    }

    private function applyDiscountedProductFilters(Builder $query): void
    {
        $query->where('is_active', true)
            ->available()
            ->withAnyActiveDiscount();
    }

    private function activeProductDiscountPercentageSubquery(): Builder
    {
        return Discount::query()
            ->selectRaw('MAX(discount_percentage)')
            ->whereColumn('discounts.product_id', 'products.id')
            ->active();
    }

    private function activeCategoryDiscountPercentageSubquery(): Builder
    {
        return Discount::query()
            ->selectRaw('MAX(discount_percentage)')
            ->whereColumn('discounts.category_id', 'products.category_id')
            ->active();
    }
}
