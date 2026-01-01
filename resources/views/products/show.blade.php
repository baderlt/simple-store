@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Product Gallery -->
            <div class="space-y-4">
                <!-- Main Image -->
                <div class="relative overflow-hidden rounded-2xl bg-white shadow-lg border border-gray-100">
                    <img id="mainImage" 
                         loading="lazy"
                         src="{{ $product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&h=800&fit=crop' }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-[500px] object-cover transition-all duration-500">
                    
                    <!-- Discount Badge -->
                    @if($product->hasDiscount())
                        <div class="absolute top-4 left-4 z-10">
                            <div class="relative">
                                <span class="bg-gradient-to-r from-rose-500 to-pink-600 text-white px-6 py-3 rounded-xl font-bold text-lg shadow-2xl">
                                    -{{ $product->activeDiscount->discount_percentage }}%
                                </span>
                                <div class="absolute -inset-1 bg-gradient-to-r from-rose-500 to-pink-600 rounded-xl blur opacity-30 -z-10"></div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Stock Warning -->
                    @if($product->isLowStock() && $product->stock_quantity > 0)
                        <div class="absolute top-4 right-4">
                            <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-lg">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Seulement {{ $product->stock_quantity }} restant(s)
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Thumbnails -->
                @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($product->images as $image)
                            <button type="button" onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}')" 
                                    class="group relative overflow-hidden rounded-xl border-2 border-gray-200 hover:border-emerald-500 transition-all duration-300">
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     loading="lazy"
                                     class="w-full h-24 object-cover group-hover:scale-110 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <!-- Category & Brand -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('products.index', ['category' => $product->category_id]) }}" 
                       class="inline-flex items-center bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm font-semibold hover:bg-emerald-100 transition-colors">
                        <i class="fas fa-tag mr-2"></i>
                        {{ $product->category->name }}
                    </a>
                    
                    <!-- SKU -->
                    @if($product->sku)
                        <span class="text-gray-500 text-sm">
                            <i class="fas fa-hashtag mr-1"></i> Réf: {{ $product->sku }}
                        </span>
                    @endif
                </div>

                <!-- Product Name -->
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h1>

                <!-- Rating & Reviews -->
                @php
                    $avis = rand(50, 223);
                    $star = rand(2, 9);
                    $add = rand(30, 100);
                @endphp

                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="flex text-amber-400 text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <span class="ml-2 text-gray-600">
                            4.{{$star}} ({{ $avis }} avis)
                        </span>
                    </div>

                    <div class="text-sm text-gray-500">
                        <i class="fas fa-shopping-cart mr-1"></i>
                        {{ $avis + $add }} vendus
                    </div>
                </div>

                <!-- Price Section -->
                <div class="py-4 border-y border-gray-200">
                    @if($product->hasDiscount())
                        <div class="flex items-baseline space-x-4 mb-2">
                            <span class="text-5xl font-bold text-gray-900">{{ number_format($product->final_price, 2) }} DH</span>
                            <span class="text-2xl text-gray-400 line-through">{{ number_format($product->price, 2) }} DH</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-lg font-bold">
                                Économisez {{ number_format($product->price - $product->final_price, 2) }} DH
                            </span>
                            @if($product->activeDiscount->end_date)
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-clock mr-1"></i>
                                    Offre expire le {{ $product->activeDiscount->end_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    @else
                        <span class="text-5xl font-bold text-gray-900">{{ number_format($product->price, 2) }} DH</span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            @if($product->stock_quantity > 0)
                                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                                <span class="font-semibold text-emerald-700">Disponible en stock</span>
                            @else
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="font-semibold text-red-700">Rupture de stock</span>
                            @endif
                        </div>
                        @if($product->stock_quantity > 0)
                            <span class="text-sm text-gray-600">
                                {{ $product->stock_quantity }} unités disponibles
                            </span>
                        @endif
                    </div>
                    
                    <!-- Stock Progress Bar -->
                    @if($product->stock_quantity > 0)
                        <div class="mt-3">
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full" 
                                     style="width: {{ min(($product->stock_quantity / 100) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quantity Selector -->
                @if($product->stock_quantity > 0)
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                            <div class="flex items-center space-x-3">
                                <button type="button" onclick="updateQuantity(-1)" 
                                        class="w-12 h-12 border border-gray-300 rounded-lg hover:border-emerald-500 hover:bg-emerald-50 transition-colors flex items-center justify-center">
                                    <i class="fas fa-minus text-gray-600"></i>
                                </button>
                                <input type="number" 
                                       id="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="{{ $product->stock_quantity }}"
                                       class="w-24 h-12 text-center border border-gray-300 rounded-lg text-lg font-semibold focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                                <button type="button" onclick="updateQuantity(1)" 
                                        class="w-12 h-12 border border-gray-300 rounded-lg hover:border-emerald-500 hover:bg-emerald-50 transition-colors flex items-center justify-center">
                                    <i class="fas fa-plus text-gray-600"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                    <input type="hidden" name="quantity" id="formQuantity" value="1">
                    <button type="button" 
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-stock="{{ $product->stock_quantity }}"
                    class="add-to-cart-btn w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-4 rounded-xl font-bold text-lg hover:from-emerald-600 hover:to-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center group">
                   <i class="fas fa-shopping-cart mr-3 group-hover:rotate-12 transition-transform"></i>
                                    Ajouter au panier
            </button>


       <form action="{{ route('checkout.direct', $product->id) }}" method="GET" id="buyNowForm">
    <input type="hidden" name="quantity" id="buyNowQuantity" value="1">
    <button type="submit" 
            class="w-full bg-gradient-to-r from-gray-900 to-black text-white py-4 rounded-xl font-bold text-lg hover:from-gray-800 hover:to-gray-900 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center group">
        <i class="fas fa-bolt mr-3 group-hover:scale-125 transition-transform"></i>
        Commander maintenant
    </button>
</form>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex items-center justify-center space-x-6 pt-4">
                            <button type="button" onclick="shareProduct()" 
                                    class="flex items-center text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-share-alt text-xl mr-2"></i>
                                <span class="text-sm">Partager</span>
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Notify Me Button -->
                    <div class="text-center space-y-4">
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                            <p class="text-amber-800 font-medium">
                                <i class="fas fa-bell mr-2"></i>
                                Ce produit est actuellement en rupture de stock
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Features & Guarantees -->
                <div class="grid grid-cols-2 gap-4 pt-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shipping-fast text-blue-600"></i>
                        </div>
                        @if (settings('free_delivery_threshold'))
                        <div>
                            <p class="font-medium text-gray-900">Livraison gratuite</p>
                            <p class="text-sm text-gray-500">À partir de {{settings('free_delivery_threshold')}} DH</p>
                        </div>
                            
                        @endif
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Garantie</p>
                            <p class="text-sm text-gray-500">1 ans</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-undo-alt text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Retours</p>
                            <p class="text-sm text-gray-500">15 jours</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-headset text-orange-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Support</p>
                            <p class="text-sm text-gray-500">24/7</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description & Details Tabs -->
        <div class="mt-12">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button type="button" onclick="switchTab('description')" 
                            id="tab-description" 
                            class="py-4 px-1 border-b-2 font-medium text-sm border-emerald-500 text-emerald-600">
                        <i class="fas fa-file-alt mr-2"></i>Description
                    </button>
                    <button type="button" onclick="switchTab('shipping')" 
                            id="tab-shipping" 
                            class="py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-truck mr-2"></i>Livraison
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div id="tab-content" style="display: block" class="py-8">
                <!-- Description Tab -->
                <div id="description-content"  class="space-y-6">
                    <p class="text-gray-700 leading-relaxed text-lg">{{ $product->description }}</p>
                    
                    <!-- Features List -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <h4 class="font-bold text-gray-900">Caractéristiques principales</h4>
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-emerald-500 mr-3"></i>
                                    <span>Qualité premium garantie</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-emerald-500 mr-3"></i>
                                    <span>Matériaux durables</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-emerald-500 mr-3"></i>
                                    <span>Design moderne et élégant</span>
                                </li>
                            </ul>
                        </div>
                        <div class="space-y-3">
                            <h4 class="font-bold text-gray-900">Avantages</h4>
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-emerald-500 mr-3"></i>
                                    <span>Facile à utiliser</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-emerald-500 mr-3"></i>
                                    <span>Entretien simple</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-emerald-500 mr-3"></i>
                                    <span>Écologique</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Shipping Tab (Hidden by default) -->
                <div id="shipping-content" class="hidden space-y-6">
                    <div class="bg-gray-50 p-6 rounded-xl">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Informations de livraison</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-2">Délais de livraison</h4>
                                <ul class="space-y-2 text-gray-600">
                                    <li class="flex items-center">
                                        <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                                        <span>Fes : 24 heures</span>
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                                        <span>Grandes villes : 1-3 jours</span>
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                                        <span>Autres régions : 3-5 jours</span>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-2">Frais de livraison</h4>
                                <ul class="space-y-2 text-gray-600">
                                    <li class="flex items-center">
                                        <i class="fas fa-truck text-blue-500 mr-2"></i>
                                        <span>Livraison standard : {{settings('delivery_fee')}} DH</span>
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-rocket text-purple-500 mr-2"></i>
                                        <span>Livraison express : 50 DH</span>
                                    </li>
                                    @if (settings('free_delivery_threshold'))
                                    <li class="flex items-center">
                                        <i class="fas fa-gift text-amber-500 mr-2"></i>
                                        <span>Offerte à partir de {{settings('free_delivery_threshold')}} DH</span>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <section class="mt-16">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Vous aimerez aussi</h2>
                        <p class="text-gray-600 mt-2">Découvrez des produits similaires</p>
                    </div>
                    <a href="{{ route('products.index', ['category' => $product->category_id]) }}" 
                       class="text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center">
                        Voir tout
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    @foreach($relatedProducts as $related)
                        <div class="group bg-white border border-gray-100 rounded-xl hover:border-emerald-200 hover:shadow-xl transition-all duration-300 overflow-hidden">
                            <a href="{{ route('products.show', $related->slug) }}" class="block">
                                <div class="aspect-square bg-gray-50 overflow-hidden">
                                    <img src="{{ $related->primaryImage ? asset('storage/' . $related->primaryImage->image_path) : 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop' }}" 
                                         alt="{{ $related->name }}" 
                                         loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                            </a>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 text-sm mb-2 line-clamp-2">{{ $related->name }}</h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-gray-900">{{ number_format($related->final_price, 2) }} DH</span>
                                    @if($related->stock_quantity > 0)
     
                                           <button type="button" 
                    data-product-id="{{ $related->id }}"
                    data-product-name="{{ $related->name }}"
                    data-product-stock="{{ $related->stock_quantity }}"
                    class="add-to-cart-btn w-8 h-8 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-colors">
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                  
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
<script src="{{asset("js/product.js")}}"></script>
<style>
    /* Smooth image transition */
    #mainImage {
        transition: opacity 0.3s ease-in-out;
    }
    
    /* Custom input number styling */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    /* Tab animation */
    [id$="-content"] {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Button hover effects */
    .group:hover .group-hover\:rotate-12 {
        transform: rotate(12deg);
    }
</style>

@endsection