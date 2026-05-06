@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Product Gallery -->
            <div class="space-y-6">
                <!-- Main Image Container -->
                <div class="relative group">
                    <div class="relative overflow-hidden rounded-2xl bg-white shadow-2xl border border-gray-100">
                        <img id="mainImage" 
                             loading="lazy"
                             src="{{ $product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&h=800&fit=crop' }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-[500px] object-cover transition-all duration-500 group-hover:scale-[1.02]">
                        
                        <!-- Zoom Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button type="button" onclick="openImageModal()" 
                                    class="absolute bottom-6 right-6 bg-white/90 backdrop-blur-sm w-12 h-12 rounded-full flex items-center justify-center text-gray-700 hover:bg-white hover:text-emerald-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-110">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </button>
                        </div>
                        
                        <!-- Discount Badge -->
                        @if($product->hasDiscount())
                            <div class="absolute top-6 left-6 z-10">
                                <div class="relative">
                                    <span class="bg-gradient-to-r from-rose-500 to-pink-600 text-white px-5 py-2.5 rounded-xl font-bold text-base shadow-2xl">
                                        -{{ $product->activeDiscount->discount_percentage }}%
                                    </span>
                                    <div class="absolute -inset-1 bg-gradient-to-r from-rose-500 to-pink-600 rounded-xl blur opacity-30 -z-10"></div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Stock Warning -->
                        @if($product->isLowStock() && $product->stock_quantity > 0)
                            <div class="absolute top-6 right-6">
                                <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-lg backdrop-blur-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    {{ $product->stock_quantity }} restant(s)
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Thumbnails Grid -->
                @if($product->images->count() > 1)
                    <div class="relative">
                        @if($product->images->count() > 4)
                            <!-- Navigation Arrows -->
                            <button type="button" onclick="scrollThumbnails(-1)" 
                                    class="absolute left-0 top-1/2 transform -translate-y-1/2 -ml-6 w-10 h-10 bg-white rounded-full shadow-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:text-emerald-600 hover:border-emerald-300 z-10 transition-all duration-200 hover:shadow-xl">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" onclick="scrollThumbnails(1)" 
                                    class="absolute right-0 top-1/2 transform -translate-y-1/2 -mr-6 w-10 h-10 bg-white rounded-full shadow-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:text-emerald-600 hover:border-emerald-300 z-10 transition-all duration-200 hover:shadow-xl">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif

                        <!-- Thumbnails Container -->
                        <div id="thumbnailsContainer" class="flex overflow-x-auto space-x-3 pb-2 scrollbar-hide snap-x snap-mandatory">
                            @foreach($product->images as $index => $image)
                                <button type="button" 
                                        onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}', {{ $index + 1 }})" 
                                        class="group relative flex-shrink-0 snap-center">
                                    <div class="relative overflow-hidden rounded-xl border-2 border-gray-200 hover:border-emerald-500 transition-all duration-200 w-20 h-20 lg:w-28 lg:h-28">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             alt="{{ $product->name }} - Image {{ $index + 1 }}" 
                                             loading="lazy"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        
                                        <!-- Active Indicator -->
                                        @if($loop->first)
                                            <div class="absolute inset-0 border-2 border-emerald-500 rounded-xl pointer-events-none"></div>
                                            <div class="absolute top-2 right-2 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Image Counter (Discreet) -->
                @if($product->images->count() > 1)
                    <div class="text-center">
                        <div class="inline-flex items-center space-x-2 bg-gray-50 px-4 py-2 rounded-full">
                            <i class="fas fa-image text-gray-400 text-sm"></i>
                            <span class="text-sm text-gray-600 font-medium">
                                Image <span class="text-emerald-600" id="currentImageIndex">1</span> / {{ $product->images->count() }}
                            </span>
                        </div>
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
                    
                    @if($product->sku)
                        <span class="text-gray-500 text-sm">
                            <i class="fas fa-hashtag mr-1"></i> Réf: {{ $product->sku }}
                        </span>
                    @endif
                </div>

                <!-- Product Name -->
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h1>

                <!-- Rating & Reviews -->
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <div class="flex text-amber-400">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <span class="ml-2 text-gray-600 font-medium">
                            4.{{ rand(2, 9) }} ({{ rand(50, 223) }} avis)
                        </span>
                    </div>
                    <div class="text-gray-500">
                        <i class="fas fa-shopping-cart mr-1"></i>
                        {{ rand(80, 300) }} vendus
                    </div>
                </div>

                <!-- Price Section -->
                <div class="py-6 border-y border-gray-200">
                    @if($product->hasDiscount())
                        <div class="flex items-baseline space-x-4 mb-3">
                            <span class="text-5xl font-bold text-gray-900">{{ number_format($product->final_price, 2) }} DH</span>
                            <span class="text-2xl text-gray-400 line-through">{{ number_format($product->price, 2) }} DH</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="bg-rose-50 text-rose-700 px-3 py-1.5 rounded-lg font-bold text-sm">
                                Économisez {{ number_format($product->price - $product->final_price, 2) }} DH
                            </span>
                            @if($product->activeDiscount->end_date)
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-clock mr-1"></i>
                                    Jusqu'au {{ $product->activeDiscount->end_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    @else
                        <span class="text-5xl font-bold text-gray-900">{{ number_format($product->price, 2) }} DH</span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
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
                            <span class="text-sm text-gray-600 font-medium">
                                {{ $product->stock_quantity }} unités disponibles
                            </span>
                        @endif
                    </div>
                    
                    @if($product->stock_quantity > 0)
                        <div class="space-y-2">
                            <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full" 
                                     style="width: {{ min(($product->stock_quantity / 100) * 100, 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Stock limité</span>
                                <span>Disponible</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quantity Selector -->
                @if($product->stock_quantity > 0)
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Quantité</label>
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
                                    class="add-to-cart-btn w-full bg-green-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center group">
                                <i class="fas fa-shopping-cart mr-3 group-hover:rotate-12 transition-transform"></i>
                                Ajouter au panier
                            </button>

                            <form action="{{ route('checkout.direct', $product->id) }}" method="GET" id="buyNowForm">
                                <input type="hidden" name="quantity" id="buyNowQuantity" value="1">
                                <button type="submit" 
                                        class="w-full bg-gray-900 text-white py-4 rounded-xl font-bold text-lg hover:bg-gray-800 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center group">
                                    <i class="fas fa-bolt mr-3 group-hover:scale-125 transition-transform"></i>
                                    Commander maintenant
                                </button>
                            </form>
                        </div>

                        <!-- Fixed buy action (shown after scrolling past the original action buttons) -->
                        <div id="mobileBuyNowBar"
                             class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur border-t border-gray-200 shadow-2xl p-3 transform translate-y-full opacity-0 pointer-events-none transition-all duration-300">
                            <form action="{{ route('checkout.direct', $product->id) }}" method="GET" id="fixedBuyNowForm">
                                <input type="hidden" name="quantity" id="fixedBuyNowQuantity" value="1">
                                <button type="submit"
                                        class="w-full bg-gray-900 text-white py-3 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all duration-300 shadow flex items-center justify-center">
                                    <i class="fas fa-bolt mr-2"></i>
                                    Commander maintenant
                                </button>
                            </form>
                        </div>

                        <!-- Share Button -->
                        <div class="flex justify-center pt-4">
                            <button type="button" onclick="shareProduct()" 
                                    class="inline-flex items-center text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-share-alt text-lg mr-2"></i>
                                <span class="text-sm font-medium">Partager ce produit</span>
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
                    <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shipping-fast text-blue-600"></i>
                        </div>
                        @if(settings('free_delivery_threshold'))
                        <div>
                            <p class="font-medium text-gray-900">Livraison gratuite</p>
                            <p class="text-sm text-gray-500">À partir de {{settings('free_delivery_threshold')}} DH</p>
                        </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Garantie</p>
                            <p class="text-sm text-gray-500">1 an</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-undo-alt text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Retours</p>
                            <p class="text-sm text-gray-500">15 jours</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
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
                <div id="description-content" class="space-y-6">
                    <p class="text-gray-700 leading-relaxed text-lg">{{ $product->description }}</p>
                    
                    <!-- Features List -->
                    <div class="grid md:grid-cols-2 gap-6">
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
                        <h2 class="text-3xl font-bold text-gray-900">Produits similaires</h2>
                        <p class="text-gray-600 mt-2">Vous pourriez également aimer</p>
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

<!-- Image Modal for Fullscreen View -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-95 z-50 flex items-center justify-center p-4">
    <div class="relative w-full max-w-6xl h-full flex items-center justify-center">
        <!-- Close Button -->
        <button type="button" onclick="closeImageModal()" 
                class="absolute top-6 right-6 w-10 h-10 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-colors z-20">
            <i class="fas fa-times"></i>
        </button>
        
        <!-- Navigation Arrows -->
        <button type="button" onclick="navigateModalImage(-1)" 
                class="absolute left-6 w-10 h-10 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-colors z-20">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button type="button" onclick="navigateModalImage(1)" 
                class="absolute right-6 w-10 h-10 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-colors z-20">
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <!-- Main Modal Image -->
        <img id="modalImage" class="max-w-full max-h-full object-contain" alt="">
        
        <!-- Image Counter -->
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-black/50 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm">
            <span id="modalImageInfo">1 / {{ $product->images->count() }}</span>
        </div>
        
        <!-- Thumbnails in Modal -->
        @if($product->images->count() > 1)
            <div class="absolute bottom-20 left-1/2 transform -translate-x-1/2 flex space-x-2 overflow-x-auto max-w-full p-2">
                @foreach($product->images as $index => $image)
                    <button type="button" 
                            onclick="changeModalImage('{{ asset('storage/' . $image->image_path) }}', {{ $index + 1 }})" 
                            class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-white transition-colors">
                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                             alt="Thumbnail {{ $index + 1 }}" 
                             class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script src="{{asset("js/product.js")}}"></script>
<script>
// Gallery functionality
let currentImageIndex = 1;
const totalImages = {{ $product->images->count() }};
const images = [
    @foreach($product->images as $image)
        '{{ asset('storage/' . $image->image_path) }}',
    @endforeach
];

// Change main image
function changeMainImage(src, index) {
    const mainImage = document.getElementById('mainImage');
    const currentIndex = document.getElementById('currentImageIndex');
    
    // Add fade out effect
    mainImage.style.opacity = '0';
    
    setTimeout(() => {
        mainImage.src = src;
        mainImage.style.opacity = '1';
        currentImageIndex = index;
        currentIndex.textContent = index;
        
        // Update active thumbnail
        updateActiveThumbnail(index);
    }, 200);
}

// Update active thumbnail indicator
function updateActiveThumbnail(index) {
    // Remove all active indicators
    document.querySelectorAll('#thumbnailsContainer button').forEach(btn => {
        btn.querySelector('.relative').classList.remove('border-emerald-500');
        const checkIcon = btn.querySelector('.bg-emerald-500');
        if (checkIcon) {
            checkIcon.remove();
        }
    });
    
    // Add active indicator to current thumbnail
    const currentThumbnail = document.querySelector(`#thumbnailsContainer button:nth-child(${index})`);
    if (currentThumbnail) {
        const thumbnailDiv = currentThumbnail.querySelector('.relative');
        thumbnailDiv.classList.add('border-emerald-500');
        
        const checkIcon = document.createElement('div');
        checkIcon.className = 'absolute top-2 right-2 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center';
        checkIcon.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
        thumbnailDiv.appendChild(checkIcon);
    }
}

// Scroll thumbnails
function scrollThumbnails(direction) {
    const container = document.getElementById('thumbnailsContainer');
    const scrollAmount = 120; // width of thumbnail + gap
    container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}

// Image modal functions
function openImageModal() {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalImageInfo = document.getElementById('modalImageInfo');
    
    modalImage.src = images[currentImageIndex - 1];
    modalImageInfo.textContent = `${currentImageIndex} / ${totalImages}`;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function navigateModalImage(direction) {
    let newIndex = currentImageIndex + direction;
    if (newIndex < 1) newIndex = totalImages;
    if (newIndex > totalImages) newIndex = 1;
    
    const modalImage = document.getElementById('modalImage');
    const modalImageInfo = document.getElementById('modalImageInfo');
    
    modalImage.style.opacity = '0';
    setTimeout(() => {
        modalImage.src = images[newIndex - 1];
        modalImage.style.opacity = '1';
        currentImageIndex = newIndex;
        modalImageInfo.textContent = `${newIndex} / ${totalImages}`;
        updateActiveThumbnail(newIndex);
    }, 200);
}

function changeModalImage(src, index) {
    const modalImage = document.getElementById('modalImage');
    const modalImageInfo = document.getElementById('modalImageInfo');
    
    modalImage.style.opacity = '0';
    setTimeout(() => {
        modalImage.src = src;
        modalImage.style.opacity = '1';
        currentImageIndex = index;
        modalImageInfo.textContent = `${index} / ${totalImages}`;
        updateActiveThumbnail(index);
    }, 200);
}

// Keyboard navigation for modal
document.addEventListener('keydown', (e) => {
    const modal = document.getElementById('imageModal');
    if (!modal.classList.contains('hidden')) {
        if (e.key === 'Escape') {
            closeImageModal();
        } else if (e.key === 'ArrowLeft') {
            navigateModalImage(-1);
        } else if (e.key === 'ArrowRight') {
            navigateModalImage(1);
        }
    }
});

// Tab switching function
function switchTab(tabName) {
    // Update active tab
    document.querySelectorAll('[id^="tab-"]').forEach(tab => {
        tab.classList.remove('border-emerald-500', 'text-emerald-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    const activeTab = document.getElementById(`tab-${tabName}`);
    activeTab.classList.add('border-emerald-500', 'text-emerald-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    
    // Show active content
    document.querySelectorAll('[id$="-content"]').forEach(content => {
        content.classList.add('hidden');
    });
    
    const activeContent = document.getElementById(`${tabName}-content`);
    activeContent.classList.remove('hidden');
}

// Quantity update
function updateQuantity(change) {
    const input = document.getElementById('quantity');
    const formQuantity = document.getElementById('formQuantity');
    const buyNowQuantity = document.getElementById('buyNowQuantity');
    const fixedBuyNowQuantity = document.getElementById('fixedBuyNowQuantity');
    
    let newValue = parseInt(input.value) + change;
    newValue = Math.max(1, Math.min(newValue, {{ $product->stock_quantity }}));
    
    input.value = newValue;
    formQuantity.value = newValue;
    buyNowQuantity.value = newValue;
    if (fixedBuyNowQuantity) {
        fixedBuyNowQuantity.value = newValue;
    }
}

// Sticky buy-now visibility
function updateMobileBuyNowBar() {
    const bar = document.getElementById('mobileBuyNowBar');
    const originalForm = document.getElementById('buyNowForm');

    if (!bar || !originalForm) {
        return;
    }

    const originalButtonBottom = originalForm.getBoundingClientRect().bottom;
    const shouldShow = originalButtonBottom < 0;

    bar.classList.toggle('translate-y-full', !shouldShow);
    bar.classList.toggle('opacity-0', !shouldShow);
    bar.classList.toggle('pointer-events-none', !shouldShow);
}

window.addEventListener('scroll', updateMobileBuyNowBar, { passive: true });
window.addEventListener('resize', updateMobileBuyNowBar);
document.addEventListener('DOMContentLoaded', updateMobileBuyNowBar);

// Share product
function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $product->name }}',
            text: 'Découvrez ce produit sur notre site !',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Lien copié dans le presse-papier !');
        });
    }
}

// Initialize active thumbnail
updateActiveThumbnail(1);
</script>

<style>
    body {
        padding-bottom: 84px;
    }

    /* Smooth image transition */
    #mainImage {
        transition: opacity 0.3s ease-in-out, transform 0.5s ease;
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
    
    /* Hide scrollbar for thumbnails */
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    /* Modal animations */
    #imageModal {
        animation: fadeInModal 0.3s ease-out;
    }
    
    @keyframes fadeInModal {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Image modal image animation */
    #modalImage {
        transition: opacity 0.3s ease-in-out;
    }
    
    /* Thumbnail snap scrolling */
    .snap-x {
        scroll-snap-type: x mandatory;
    }
    
    .snap-center {
        scroll-snap-align: center;
    }
    
    /* Line clamp for product titles */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Subtle hover effects */
    .hover-lift {
        transition: transform 0.2s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-2px);
    }
</style>
@endsection