@extends('layouts.app')

@section('title', 'Produits')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 pb-16">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Mobile Filter Toggle -->
            <div class="lg:hidden mb-4">
                <button onclick="toggleFilters()" 
                        class="w-full bg-white py-3 px-4 rounded-xl shadow flex items-center justify-between">
                    <span class="font-semibold">Filtres et Catégories</span>
                    <i class="fas fa-chevron-down transition-transform" id="filterIcon"></i>
                </button>
            </div>

            {{-- Sidebar Filters --}}
            <aside class="lg:w-1/4 hidden lg:block" id="filterSidebar">
                <div class="sticky top-6 space-y-6">
                    <!-- Categories Card -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-bold text-xl text-gray-800">Catégories</h2>
                            <i class="fas fa-tags text-emerald-500"></i>
                        </div>
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('products.index') }}" 
                                   class="flex items-center justify-between p-3 rounded-lg hover:bg-emerald-50 transition-colors {{ !request('category') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700' }}">
                                    <span>Toutes les catégories</span>
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-sm">
                                        {{ \App\Models\Product::where('is_active', true)->count() }}
                                    </span>
                                </a>
                            </li>
                            @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                                       class="flex items-center justify-between p-3 rounded-lg hover:bg-emerald-50 transition-colors {{ request('category') == $category->id ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700' }}">
                                        <span>{{ $category->name }}</span>
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-sm">
                                            {{ $category->products->where('is_active', true)->count() }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Sort Card -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-bold text-xl text-gray-800">Trier par</h2>
                            <i class="fas fa-sort-amount-down text-emerald-500"></i>
                        </div>
                        <select onchange="window.location.href='{{ route('products.index') }}?' + updateQueryString('sort', this.value)" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 transition-all">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récents</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        </select>
                    </div>

                    <!-- Stats Card -->
                    <div class="hidden lg:flex bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl shadow-lg p-6 text-white">
                        <h3 class="font-bold text-lg mb-4">Info Boutique</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span>Produits disponibles</span>
                                <span class="font-bold">{{ $products->total() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Catégories</span>
                                <span class="font-bold">{{ $categories->count() }}</span>
                            </div>
              
                            @if (settings('free_delivery_threshold'))
                            <div class="pt-3 border-t border-emerald-400">
                                
                                <p class="text-sm opacity-90">Livraison gratuite à partir de {{settings('free_delivery_threshold')}} DH</p>
                            </div>
                                
                            @endif
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Products Grid --}}
            <main class="lg:w-3/4">
                <!-- Results Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 bg-white p-4 rounded-xl shadow-sm">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Produits</h2>
                        <p class="text-gray-600">
                            {{ $products->total() }} produit{{ $products->total() > 1 ? 's' : '' }} trouvé{{ $products->total() > 1 ? 's' : '' }}
                            @if(request('category'))
                                dans "{{ $categories->find(request('category'))->name ?? '' }}"
                            @endif
                        </p>
                    </div>
                    
                    @if(request('search') || request('category') || request('sort') != 'newest')
                    <a href="{{ route('products.index') }}" 
                       class="mt-3 sm:mt-0 text-emerald-600 hover:text-emerald-700 font-medium flex items-center">
                        <i class="fas fa-times mr-2"></i> Réinitialiser les filtres
                    </a>
                    @endif
                </div>

              {{-- Elegant card with sophisticated hover effects --}}
@if($products->count() > 0)
    <div class="">
        <!-- Products Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            @foreach($products as $product)
                <div class="group relative bg-gradient-to-br from-white to-gray-50 rounded-2xl border border-gray-100 hover:border-emerald-200 transition-all duration-300 hover:shadow-xl overflow-hidden">
                    <!-- Premium Ribbon -->
                    @if($product->hasDiscount())
                        <div class="absolute top-3 left-0 z-10">
                            <div class="relative bg-gradient-to-r from-rose-500 to-pink-600 text-white py-1 px-1  rounded-r-lg shadow-lg">
                                <span class="font-bold text-xs lg:text-sm">-{{ $product->activeDiscount->discount_percentage }}%</span>
                            </div>
                        </div>
                    @endif
                    <!-- Product Image -->
                    <div class="relative overflow-hidden aspect-square">
                        <a href="{{ route('products.show', $product->slug) }}" class="block">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-gem text-gray-300 text-5xl mb-2"></i>
                                        <p class="text-gray-400 text-sm">Image non disponible</p>
                                    </div>
                                </div>
                            @endif
                        </a>
                        
                        <!-- Stock Status Overlay -->
                        @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-amber-500/90 to-transparent text-white p-3 text-center">
                                <div class="flex items-center justify-center space-x-2 text-sm font-semibold">
                                    <i class="fas fa-bolt"></i>
                                    <span>Seulement {{ $product->stock_quantity }} restant(s)</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Product Content -->
                    <div class="p-2 md:p-5">
                        <!-- Category -->
                        <div class="mb-3">
                            <a href="{{ route('products.index', ['category' => $product->category_id]) }}" 
                               class="inline-flex items-center text-[10px] text-emerald-600 font-semibold uppercase tracking-wider hover:text-emerald-700">
                                <i class="fas fa-tag mr-1.5"></i>
                                {{ $product->category->name ?? 'Catégorie' }}
                            </a>
                        </div>
                        
                        <!-- Product Name -->
                        <h3 class="font-bold text-gray-900 text-sm sm:text-base mb-2 sm:mb-3 line-clamp-2 group-hover:text-emerald-700 transition-colors leading-tight sm:px-0">
                            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-emerald-700">
                                {{ $product->name }}
                            </a>
                        </h3>
                        <!-- Price -->
                        <div class="flex items-center justify-between mb-3 lg:mb-5">
                            @if($product->hasDiscount())
                                <div class="flex ">
                                    <span class="text-red-400 text-x line-through mr-2">{{ number_format($product->price, 0) }} DH</span>
                                    <span class="text-xl font-bold text-gray-900">{{ number_format($product->final_price, 0) }} DH</span>
                               
                            
                                </div>
                            @else
                                <div class="text-xl font-bold text-gray-900">{{ number_format($product->price, 2) }} DH</div>
                            @endif
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <!-- View Details -->
                            <a href="{{ route('products.show', $product->slug) }}" 
                               class="flex-1 text-center bg-gray-100 text-gray-700 hover:bg-gray-200 py-2 rounded-xl font-medium text-sm transition-colors duration-300">
                                <i class="fas fa-eye mr-2"></i>Détails
                            </a>
                            
                            <!-- Add to Cart -->
                            @if($product->stock_quantity > 0)
                               <button type="button" 
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-stock="{{ $product->stock_quantity }}"
                    class="add-to-cart-btn w-10 h-10   bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center group/btn">
                <i class="fas fa-shopping-cart group-hover/btn:scale-110 transition-transform"></i>
            </button>
                            @else
                                <button disabled 
                                        class="w-6 h-6 md:w-12 md:h-12 bg-gray-200 text-gray-400 rounded-xl cursor-not-allowed flex items-center justify-center">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Hover Effect Border -->
                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-emerald-300 rounded-2xl transition-all duration-300 pointer-events-none"></div>
                </div>
            @endforeach
        </div>
    </div>
@else
    
                    <!-- Empty State -->
                    <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-emerald-50 mb-6">
                            <i class="fas fa-box-open text-4xl text-emerald-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">Aucun produit trouvé</h3>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">
                            Nous n'avons trouvé aucun produit correspondant à vos critères. Essayez de modifier vos filtres.
                        </p>
                        <a href="{{ route('products.index') }}" 
                           class="inline-flex items-center bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-8 py-3 rounded-full font-semibold hover:from-emerald-600 hover:to-teal-700 transition-all">
                            <i class="fas fa-redo mr-3"></i> Réinitialiser les filtres
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>


<style>
    .animate-blob {
        animation: blob 7s infinite;
    }
    
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    
    .animation-delay-4000 {
        animation-delay: 4s;
    }
    
    @keyframes blob {
        0% {
            transform: translate(0px, 0px) scale(1);
        }
        33% {
            transform: translate(30px, -50px) scale(1.1);
        }
        66% {
            transform: translate(-20px, 20px) scale(0.9);
        }
        100% {
            transform: translate(0px, 0px) scale(1);
        }
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<script>
    function toggleFilters() {
        const sidebar = document.getElementById('filterSidebar');
        const icon = document.getElementById('filterIcon');
        sidebar.classList.toggle('hidden');
        icon.classList.toggle('fa-chevron-down');
        icon.classList.toggle('fa-chevron-up');
    }

    function updateQueryString(key, value) {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        
        if (value) {
            params.set(key, value);
        } else {
            params.delete(key);
        }
        
        // Keep other existing parameters
        ['search', 'category'].forEach(param => {
            if (url.searchParams.get(param)) {
                params.set(param, url.searchParams.get(param));
            }
        });
        
        return params.toString();
    }

    // Auto-close mobile filters on category click
    document.addEventListener('DOMContentLoaded', function() {
        const mobileFilterLinks = document.querySelectorAll('#filterSidebar a[href*="category"]');
        mobileFilterLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    toggleFilters();
                }
            });
        });
    });
</script>
@endsection