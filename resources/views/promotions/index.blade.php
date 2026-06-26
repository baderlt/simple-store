{{-- resources/views/promotions/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Promotions - Maison Dorée')
@section('description', 'Découvrez nos offres sur les miels, thés, parfums et produits bio')

@section('content')
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-r from-red-600 to-orange-500 text-white py-12 md:py-16">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-red-500/20 rounded-full blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center">
                <h1 class="text-3xl md:text-5xl font-bold mb-4">
                    <i class="fas fa-tags mr-3"></i>Nos Promotions
                </h1>
                <p class="text-xl md:text-2xl text-white/90 max-w-3xl mx-auto">
                    Profitez de nos meilleures offres sur les miels, thés, parfums et produits bio
                </p>
                
                {{-- Countdown Timer --}}
                <div class="mt-8 inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full">
                    <i class="fas fa-clock animate-pulse"></i>
                    <span>Offres en cours • Dernière mise à jour : {{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Bar --}}
    <section class="bg-white border-b border-gray-100 py-4">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $productsWithDiscount->total() }}</div>
                        <div class="text-sm text-gray-600">Produits en promotion</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $maxDiscount ?? 0 }}%</div>
                        <div class="text-sm text-gray-600">Réduction max</div>
                    </div>
                </div>
                
                {{-- Filters --}}
                <div class="flex flex-wrap gap-3">
                    <select id="categoryFilter" 
                            class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Toutes catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->localized_name }}</option>
                        @endforeach
                    </select>
                    
                    <select id="discountFilter" 
                            class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Toutes réductions</option>
                        <option value="10">-10% et plus</option>
                        <option value="20">-20% et plus</option>
                        <option value="30">-30% et plus</option>
                        <option value="40">-40% et plus</option>
                        <option value="50">-50% et plus</option>
                    </select>
                    
                    <select class="hidden" id="sortFilter" 
                            class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="discount_desc">Plus grande réduction</option>
                        <option value="price_asc">Prix croissant</option>
                        <option value="price_desc">Prix décroissant</option>
                        <option value="name_asc">Nom A-Z</option>
                        <option value="name_desc">Nom Z-A</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Promotions --}}
    @if($featuredProducts->count() > 0)
        <section class="py-8 bg-gradient-to-b from-orange-50 to-white">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-fire text-red-500"></i>
                    Promotions phares
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border border-orange-100">
                            {{-- Super Discount Badge --}}
                            @if($product->hasDiscount() && $product->activeDiscount)
                                <div class="absolute top-3 left-3 z-10">
                                    <span class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-3 py-1.5 rounded-full text-sm font-bold shadow-lg animate-pulse">
                                        -{{ number_format($product->activeDiscount->discount_percentage, 0) }}%
                                    </span>
                                </div>
                            @endif
                            
                            {{-- Expiration Warning --}}
                            @if($product->hasDiscount() && $product->activeDiscount && $product->activeDiscount->end_date && $product->activeDiscount->end_date->diffInDays(now()) <= 3)
                                <div class="absolute top-3 right-3 z-10">
                                    <span class="bg-yellow-500 text-white px-2 py-1 rounded text-xs font-bold">
                                        <i class="fas fa-clock"></i> Bientôt fini
                                    </span>
                                </div>
                            @endif
                            
                            {{-- Product Image --}}
                            <div class="relative overflow-hidden">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                             alt="{{ $product->name }}" 
                                             loading="lazy"
                                             decoding="async"
                                             width="600"
                                             height="400"
                                             class="w-full h-48 object-cover hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-orange-100 to-red-100 flex items-center justify-center">
                                            <i class="fas fa-image text-orange-300 text-4xl"></i>
                                        </div>
                                    @endif
                                </a>
                            </div>
                            
                            {{-- Product Details --}}
                            <div class="p-4">
                                {{-- Category --}}
                                @if($product->category)
                                    <div class="mb-2">
                                        <span class="text-xs text-green-600 font-semibold bg-green-50 px-2 py-1 rounded">
                                            {{ $product->category->localized_name }}
                                        </span>
                                    </div>
                                @endif
                                
                                {{-- Product Name --}}
                                <h3 class="font-semibold text-lg mb-2 min-h-[56px]">
                                    <a href="{{ route('products.show', $product->slug) }}" 
                                       class="bidi-auto bidi-auto-block text-gray-800 hover:text-red-600 transition-colors duration-300 line-clamp-2"
                                       dir="auto">
                                        {!! bidi_text($product->name) !!}
                                    </a>
                                </h3>
                                
                                {{-- Price --}}
                                <div class="mb-3">
                                    @if($product->hasDiscount() && $product->activeDiscount)
                                        <div class="flex items-center gap-3">
                                            <span class="text-gray-400 line-through text-sm">
                                                {{ number_format($product->price, 2) }} DH
                                            </span>
                                            <span class="text-red-600 font-bold text-xl">
                                                {{ number_format($product->final_price, 2) }} DH
                                            </span>
                                        </div>
                                        <div class="text-xs text-red-500 mt-1">
                                            <i class="fas fa-money-bill-wave"></i>
                                            Économisez {{ number_format($product->price - $product->final_price, 2) }} DH
                                        </div>
                                    @endif
                                </div>
                                 @php

    
                                    $carbonDate = Carbon\Carbon::parse($product->activeDiscount->end_date);
                                    $carbonDate->locale('fr');
                                @endphp
           
                                {{-- Discount Timer --}}
                                @if($product->hasDiscount() && $product->activeDiscount && $product->activeDiscount->end_date)
                                    <div class="mb-3">
                                        <div class="text-xs text-gray-600 mb-1">
                                            <i class="fas fa-hourglass-half"></i>
                                 Offre expire {{ $carbonDate->diffForHumans() }}
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            @php
                                                $totalDays = $product->activeDiscount->start_date->diffInDays($product->activeDiscount->end_date);
                                                $remainingDays = now()->diffInDays($product->activeDiscount->end_date, false);
                                                $passedDays = max(0, $totalDays - max(0, $remainingDays));
                                                $progress = $totalDays > 0 ? ($passedDays / $totalDays) * 100 : 100;
                                            @endphp
                                            <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Stock Status & Add to Cart --}}
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    @if($product->stock_quantity > 0)
                                        <span class="text-sm text-green-600 font-semibold flex items-center gap-1">
                                            <i class="fas fa-check-circle"></i>
                                            @if($product->stock_quantity <= 5)
                                                <span>Plus que {{ $product->stock_quantity }}</span>
                                            @else
                                                <span>En stock</span>
                                            @endif
                                        </span>
                
                                                                          <button type="button" 
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-stock="{{ $product->stock_quantity }}"
                    class="add-to-cart-btn text-sm bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-300 flex items-center gap-2">
                                                <i class="fas fa-cart-plus"></i>
                                                     Ajouter
            </button>
                                    
                                    @else
                                        <span class="text-sm text-red-500 font-semibold flex items-center gap-1">
                                            <i class="fas fa-times-circle"></i>
                                            Rupture
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- All Promotions --}}
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    Toutes les promotions
                    <span class="text-sm font-normal text-gray-600 ml-2">
                        ({{ $productsWithDiscount->total() }} produits)
                    </span>
                </h2>
                
                <div class="text-sm text-gray-600">
                    <span id="filteredCount">{{ $productsWithDiscount->count() }}</span> produits affichés
                </div>
            </div>
            
            @if($productsWithDiscount->count() > 0)
                <div id="productsContainer" class=" grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($productsWithDiscount as $product)
                        @php
                            $activeDiscount = $product->activeDiscount;
                            $discountPercentage = $activeDiscount ? $activeDiscount->discount_percentage : 0;
                        @endphp
                        
                        <div class="product-card relative bg-white rounded-xl shadow hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100"
                             data-category="{{ $product->category_id ?? '' }}"
                             data-discount="{{ $discountPercentage }}"
                             data-price="{{ $product->final_price }}"
                             data-name="{{ strtolower($product->name) }}">
                            
                            {{-- Discount Badge --}}
                            @if($product->hasDiscount() && $activeDiscount)
                                <div class="absolute top-3 right-3 z-10">
                                    <span class="bg-gradient-to-r from-red-500 to-orange-500 text-white px-3 py-1.5 rounded-full text-sm font-bold shadow-lg">
                                        -{{ number_format($discountPercentage, 0) }}%
                                    </span>
                                </div>
                            @endif
                            
                            {{-- Product Image --}}
                            <div class="relative overflow-hidden">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                             alt="{{ $product->name }}" 
                                             loading="lazy"
                                             decoding="async"
                                             width="600"
                                             height="400"
                                             class="w-full h-48 object-cover hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-300 text-4xl"></i>
                                        </div>
                                    @endif
                                </a>
                            </div>
                            
                            {{-- Product Details --}}
                            <div class="p-4">
                                {{-- Category --}}
                                @if($product->category)
                                    <div class="mb-2">
                                        <span class="text-xs text-gray-600 font-medium">
                                            {{ $product->category->localized_name }}
                                        </span>
                                    </div>
                                @endif
                                
                                {{-- Product Name --}}
                                <h3 class="font-semibold text-lg mb-2">
                                    <a href="{{ route('products.show', $product->slug) }}" 
                                       class="bidi-auto bidi-auto-block text-gray-800 hover:text-red-600 transition-colors duration-300 line-clamp-2"
                                       dir="auto">
                                        {!! bidi_text($product->name) !!}
                                    </a>
                                </h3>
                                
                                {{-- Price --}}
                                <div class="mb-3">
                                    @if($product->hasDiscount() && $activeDiscount)
                                        <div class="flex items-center gap-3">
                                            <span class="text-gray-400 line-through text-sm">
                                                {{ number_format($product->price, 2) }} DH
                                            </span>
                                            <span class="text-red-600 font-bold text-xl">
                                                {{ number_format($product->final_price, 2) }} DH
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-green-600 font-bold text-xl">
                                            {{ number_format($product->price, 2) }} DH
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Discount Info --}}
                                @if($product->hasDiscount() && $activeDiscount && $activeDiscount->end_date)
                                    <div class="text-xs text-gray-500 mb-3 p-2 bg-gray-50 rounded">
                                        <i class="fas fa-clock mr-1"></i>
                                        Jusqu'au {{ $activeDiscount->end_date->format('d/m/Y') }}
                                    </div>
                                @endif
                                
                                {{-- Actions --}}
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <a href="{{ route('products.show', $product->slug) }}" 
                                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Voir détails
                                    </a>
                                    
                                    @if($product->stock_quantity > 0)
                             
                                                                      <button type="button" 
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-stock="{{ $product->stock_quantity }}"
                    class="add-to-cart-btn text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg transition-colors duration-300">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                    @else
                                        <span class="text-xs text-red-500">Rupture</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- No Results Message (hidden by default) --}}
                <div id="noResults" class="hidden text-center py-12">
                    <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-600">Essayez de modifier vos filtres de recherche</p>
                </div>
                
                {{-- Pagination --}}
                @if($productsWithDiscount->hasPages())
                    <div class="mt-12">
                        {{ $productsWithDiscount->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-tag text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Aucune promotion disponible</h3>
                    <p class="text-gray-600 mb-6">Revenez bientôt pour découvrir nos offres spéciales</p>
                    <a href="{{ route('products.index') }}" 
                       class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors duration-300">
                        Voir tous les produits
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Categories with Discounts --}}
    @if($categoriesWithDiscounts->count() > 0)
        <section class="py-12 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Promotions par catégorie</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($categoriesWithDiscounts as $category)
                        <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                           class="bg-white rounded-lg p-4 border border-gray-200 hover:border-red-300 hover:shadow-md transition-all duration-300">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-800">{{ $category->localized_name }}</h3>
                                <span class="text-sm bg-red-100 text-red-700 px-2 py-1 rounded">
                                    {{ $category->products_count }} promo(s)
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">Profitez des réductions sur cette catégorie</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Newsletter --}}
    <section class="py-12 bg-gradient-to-r from-red-600 to-orange-500 text-white">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-4">
                    <i class="fas fa-envelope mr-2"></i>
                    Ne manquez plus aucune promotion
                </h2>
                <p class="text-lg mb-8 text-white/90">
                    Inscrivez-vous à notre newsletter pour recevoir en avant-première nos offres spéciales
                </p>
                
                <form class="flex flex-col sm:flex-row gap-4">
                    <input type="email" 
                           placeholder="Votre adresse email" 
                           class="flex-grow px-6 py-3 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-300">
                    <button type="submit" 
                            class="bg-white text-red-700 hover:bg-gray-100 px-8 py-3 rounded-full font-semibold transition-colors duration-300">
                        M'inscrire
                    </button>
                </form>
                
                <p class="text-sm text-white/80 mt-4">
                    <i class="fas fa-lock mr-1"></i> 100% confidentiel. Désinscription à tout moment.
                </p>
            </div>
        </div>
    </section>
@endsection
<style>
    .product-card {
        transition: all 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-4px);
    }
</style>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryFilter = document.getElementById('categoryFilter');
        const discountFilter = document.getElementById('discountFilter');
        const sortFilter = document.getElementById('sortFilter');
        const productsContainer = document.getElementById('productsContainer');
        const filteredCount = document.getElementById('filteredCount');
        const noResults = document.getElementById('noResults');
        const productCards = document.querySelectorAll('.product-card');
        
        function filterAndSortProducts() {
            const categoryValue = categoryFilter.value;
            // const discountValue = discountFilter.value;
            // const sortValue = sortFilter.value;
            
            let visibleCount = 0;
            
            productCards.forEach(card => {
                let show = true;
                
                // Filter by category
                if (categoryValue && card.dataset.category !== categoryValue) {
                    show = false;
                }
                card.style.display = show ? '' : 'none';
                
                if (show) {
                    visibleCount++;
                }
            });
            
            // Update counter
            filteredCount.textContent = visibleCount;
            
            // Show/hide no results message
            if (visibleCount === 0) {
                if (productsContainer) productsContainer.style.display = 'none';
                noResults.style.display = 'block';
            } else {
                if (productsContainer) productsContainer.style.display = 'grid';
                noResults.style.display = 'none';
                
                // Sort products
                if (productsContainer) {
                    const cardsArray = Array.from(productCards).filter(card => card.style.display !== 'none');
                    
                    cardsArray.sort((a, b) => {
                        switch (sortValue) {
                            case 'discount_desc':
                                return parseFloat(b.dataset.discount) - parseFloat(a.dataset.discount);
                            case 'price_asc':
                                return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                            case 'price_desc':
                                return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                            case 'name_asc':
                                return a.dataset.name.localeCompare(b.dataset.name);
                            case 'name_desc':
                                return b.dataset.name.localeCompare(a.dataset.name);
                            default:
                                return 0;
                        }
                    });
                    
                    // Reorder products in container
                    cardsArray.forEach(card => {
                        productsContainer.appendChild(card);
                    });
                }
            }
        }
        
        // Add event listeners to filters
        if (categoryFilter) categoryFilter.addEventListener('change', filterAndSortProducts);
        if (discountFilter) discountFilter.addEventListener('change', filterAndSortProducts);
        if (sortFilter) sortFilter.addEventListener('change', filterAndSortProducts);
        
        // Initialize filters
        filterAndSortProducts();
        
        // Add to cart animation
        document.querySelectorAll('form[action*="cart.add"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button');
                if (!button) return;
                
                const originalHTML = button.innerHTML;
                const originalClasses = button.className;
                
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;
                button.className = originalClasses.replace('bg-red-600', 'bg-gray-400').replace('bg-green-600', 'bg-gray-400');
                
                // Simulate API call delay
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-check"></i> Ajouté!';
                    button.className = originalClasses.replace('bg-red-600', 'bg-green-600').replace('hover:bg-red-700', 'hover:bg-green-700');
                    
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.disabled = false;
                        button.className = originalClasses;
                    }, 2000);
                }, 500);
            });
        });
    });
</script>
