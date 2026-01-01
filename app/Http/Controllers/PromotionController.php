<?php
// app/Http/Controllers/PromotionController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        // Méthode 1: Récupérer les produits qui ont une réduction active (produit OU catégorie)
        $productsWithDiscount = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where(function($query) {
                $query->whereHas('discounts', function($q) {
                    $q->where('is_active', true)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
                })->orWhereHas('category', function($q) {
                    $q->whereHas('discounts', function($q2) {
                        $q2->where('is_active', true)
                           ->where('start_date', '<=', now())
                           ->where('end_date', '>=', now());
                    });
                });
            })
            ->with(['category', 'primaryImage', 'discounts' => function($query) {
                $query->where('is_active', true)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            }])
            ->paginate(12);

        // Méthode 2: Produits phares (avec les plus grandes réductions)
        $featuredProducts = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where(function($query) {
                $query->whereHas('discounts', function($q) {
                    $q->where('is_active', true)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
                })->orWhereHas('category', function($q) {
                    $q->whereHas('discounts', function($q2) {
                        $q2->where('is_active', true)
                           ->where('start_date', '<=', now())
                           ->where('end_date', '>=', now());
                    });
                });
            })
            ->with(['category', 'primaryImage', 'discounts' => function($query) {
                $query->where('is_active', true)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            }])
            ->get()
            ->sortByDesc(function($product) {
                // Trier par pourcentage de réduction
                if ($product->activeDiscount) {
                    return $product->activeDiscount->discount_percentage;
                }
                return 0;
            })
            ->take(4);

        // Catégories qui ont des produits en promotion
        $categoriesWithDiscounts = Category::whereHas('products', function($query) {
            $query->where('is_active', true)
                  ->where('stock_quantity', '>', 0)
                  ->where(function($q) {
                      $q->whereHas('discounts', function($q2) {
                          $q2->where('is_active', true)
                             ->where('start_date', '<=', now())
                             ->where('end_date', '>=', now());
                      })->orWhereHas('category', function($q2) {
                          $q2->whereHas('discounts', function($q3) {
                              $q3->where('is_active', true)
                                 ->where('start_date', '<=', now())
                                 ->where('end_date', '>=', now());
                          });
                      });
                  });
        })
        ->withCount(['products' => function($query) {
            $query->where('is_active', true)
                  ->where('stock_quantity', '>', 0)
                  ->where(function($q) {
                      $q->whereHas('discounts', function($q2) {
                          $q2->where('is_active', true)
                             ->where('start_date', '<=', now())
                             ->where('end_date', '>=', now());
                      })->orWhereHas('category', function($q2) {
                          $q2->whereHas('discounts', function($q3) {
                              $q3->where('is_active', true)
                                 ->where('start_date', '<=', now())
                                 ->where('end_date', '>=', now());
                          });
                      });
                  });
        }])
        ->having('products_count', '>', 0)
        ->get();

        // Toutes les catégories pour le filtre
        $categories = Category::whereHas('products', function($query) {
            $query->where('is_active', true)
                  ->where('stock_quantity', '>', 0)
                  ->where(function($q) {
                      $q->whereHas('discounts', function($q2) {
                          $q2->where('is_active', true)
                             ->where('start_date', '<=', now())
                             ->where('end_date', '>=', now());
                      })->orWhereHas('category', function($q2) {
                          $q2->whereHas('discounts', function($q3) {
                              $q3->where('is_active', true)
                                 ->where('start_date', '<=', now())
                                 ->where('end_date', '>=', now());
                          });
                      });
                  });
        })->get();

        // Réduction maximale active
        $maxDiscount = Discount::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->max('discount_percentage');

        return view('promotions.index', compact(
            'productsWithDiscount',
            'featuredProducts',
            'categoriesWithDiscounts',
            'categories',
            'maxDiscount'
        ));
    }
}