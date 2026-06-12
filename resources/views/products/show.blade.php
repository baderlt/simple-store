@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
    $activeVariants = $product->variants->where('is_active', true)->values();
    $hasConfiguredVariants = $product->variants->isNotEmpty();
    $usesVariants = $activeVariants->isNotEmpty();
    $inStockVariants = $activeVariants->where('stock_quantity', '>', 0)->values();
    $defaultVariant = $usesVariants
        ? ($inStockVariants->firstWhere('is_default', true) ?: $inStockVariants->first() ?: $activeVariants->firstWhere('is_default', true) ?: $activeVariants->first())
        : null;
    $productAvailable = $hasConfiguredVariants ? $inStockVariants->isNotEmpty() : $product->stock_quantity > 0;
    $currentPrice = $product->getCurrentPrice($defaultVariant);
    $currentFinalPrice = $product->getDiscountedPrice($currentPrice);
    $currentStock = $hasConfiguredVariants && !$defaultVariant ? 0 : $product->getCurrentStock($defaultVariant);
    $variantAttributes = $usesVariants ? $activeVariants->flatMap(fn ($variant) => $variant->items)->groupBy('product_attribute_id')->map(function ($items) {
        $first = $items->first();
        return [
            'id' => $first->attribute->id,
            'name' => $first->attribute->name,
            'values' => $items->map(fn ($item) => ['id' => $item->value->id, 'value' => $item->value->value])->unique('id')->values()->all(),
        ];
    })->values() : collect();
    $primaryGalleryImage = $product->images->firstWhere('is_primary', true) ?: $product->primaryImage;
    $galleryImages = collect([$primaryGalleryImage])
        ->filter()
        ->merge($product->images->reject(fn ($image) => $primaryGalleryImage && $image->id === $primaryGalleryImage->id))
        ->values();
    $fallbackImageUrl = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&h=800&fit=crop';
    $initialImageUrl = $galleryImages->isNotEmpty()
        ? asset('storage/' . $galleryImages->first()->image_path)
        : $fallbackImageUrl;
    $galleryImageUrls = $galleryImages->map(fn ($image) => asset('storage/' . $image->image_path));
    if ($galleryImageUrls->isEmpty()) {
        $galleryImageUrls = collect([$fallbackImageUrl]);
    }

    $variantPayload = $usesVariants ? $activeVariants->map(function ($variant) use ($product) {
        return [
            'id' => $variant->id,
            'sku' => $variant->sku,
            'unit' => $variant->unit,
            'price' => (float) $variant->price,
            'final_price' => (float) $product->getDiscountedPrice((float) $variant->price),
            'stock_quantity' => $variant->stock_quantity,
            'image' => $variant->image_path ? asset('storage/' . $variant->image_path) : null,
            'is_default' => $variant->is_default,
            'values' => $variant->items->mapWithKeys(fn ($item) => [$item->attribute->id => $item->value->id])->all(),
        ];
    })->values() : collect();
@endphp
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
                             src="{{ $initialImageUrl }}"
                             alt="{{ $product->name }}" 
                             class="w-full h-[360px] sm:h-[440px] lg:h-[500px] object-contain p-4 sm:p-6 transition-all duration-500 group-hover:scale-[1.01]">
                        
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
                        @if($product->isLowStock() && $currentStock > 0)
                            <div class="absolute top-6 right-6">
                                <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-lg backdrop-blur-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    {{ $currentStock }} restant(s)
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Thumbnails Grid -->
                @if($galleryImages->count() > 1)
                    <div class="relative">
                        @if($galleryImages->count() > 4)
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
                            @foreach($galleryImages as $index => $image)
                                <button type="button" 
                                        onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}', {{ $index + 1 }})" 
                                        class="group relative flex-shrink-0 snap-center">
                                    <div class="relative overflow-hidden rounded-xl border-2 {{ $loop->first ? 'border-emerald-500' : 'border-gray-200' }} hover:border-emerald-500 transition-all duration-200 w-20 h-20 lg:w-28 lg:h-28">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             alt="{{ $product->name }} - Image {{ $index + 1 }}" 
                                             loading="lazy"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        
                                        <!-- Active Indicator -->
                                        @if($loop->first)
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
                @if($galleryImages->count() > 1)
                    <div class="text-center">
                        <div class="inline-flex items-center space-x-2 bg-gray-50 px-4 py-2 rounded-full">
                            <i class="fas fa-image text-gray-400 text-sm"></i>
                            <span class="text-sm text-gray-600 font-medium">
                                Image <span class="text-emerald-600" id="currentImageIndex">1</span> / {{ $galleryImages->count() }}
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
                            {{ number_format((float) $product->review_rating, 1) }} ({{ $product->reviews_count }} avis)
                        </span>
                    </div>
                    <div class="text-gray-500">
                        <i class="fas fa-shopping-cart mr-1"></i>
                        {{ $product->sales_count }} vendus
                    </div>
                </div>

                <!-- Price Section -->
                <div class="py-6 border-y border-gray-200">
                    @if($product->hasDiscount())
                        <div class="flex items-baseline space-x-4 mb-3">
                            <span class="text-5xl font-bold text-gray-900"><span id="variantFinalPrice">{{ number_format($currentFinalPrice, 2) }}</span> DH</span>
                            <span class="text-2xl text-gray-400 line-through"><span id="variantBasePrice">{{ number_format($currentPrice, 2) }}</span> DH</span>
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
                        <span class="text-5xl font-bold text-gray-900"><span id="variantBasePrice">{{ number_format($currentPrice, 2) }}</span> DH</span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div id="variantStockPanel" class="p-5 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            @if($currentStock > 0)
                                <div id="variantStockDot" class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                                <span id="variantStockLabel" class="font-semibold text-emerald-700">Disponible en stock</span>
                            @else
                                <div id="variantStockDot" class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span id="variantStockLabel" class="font-semibold text-red-700">Rupture de stock</span>
                            @endif
                        </div>
                        <span id="variantStockCount" class="text-sm text-gray-600 font-medium {{ $currentStock > 0 ? '' : 'hidden' }}">{{ $currentStock }} unités disponibles</span>
                    </div>
                    
                    @if($currentStock > 0)
                        <div id="variantStockDetails" class="space-y-2">
                            <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full" 
                                     style="width: {{ min(($currentStock / 100) * 100, 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Stock limité</span>
                                <span>Disponible</span>
                            </div>
                        </div>
                    @endif
                </div>


                @if($usesVariants)
                    <div class="p-5 bg-white rounded-xl border border-gray-200 space-y-4" id="variantChooser"
                         data-variants='@json($variantPayload)' data-default-id="{{ $defaultVariant?->id }}">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-gray-900">{{ __('product.select_variant') }}</h3>
                            <span class="text-xs text-gray-500"><span id="variantSku">{{ $defaultVariant?->sku }}</span><span id="variantUnit" class="ml-2">{{ $defaultVariant?->unit }}</span></span>
                        </div>
                        @foreach($variantAttributes as $attribute)
                            <div class="space-y-2" data-attribute="{{ $attribute['id'] }}">
                                <p class="text-sm font-semibold text-gray-700">{{ $attribute['name'] }}</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($attribute['values'] as $value)
                                        <button type="button"
                                                class="variant-option px-4 py-2 border border-gray-300 rounded-xl text-sm font-semibold hover:border-emerald-500 hover:bg-emerald-50 transition"
                                                data-attribute-id="{{ $attribute['id'] }}"
                                                data-value-id="{{ $value['id'] }}">
                                            {{ $value['value'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        <p id="variantMessage" class="text-sm text-red-600 hidden">{{ __('product.choose_option') }}</p>
                    </div>
                @endif

                <!-- Quantity Selector -->
                @if($productAvailable)
                    <div class="space-y-6" id="purchaseActions">
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
                                       max="{{ $currentStock }}"
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
                            <input type="hidden" name="variant_id" id="selectedVariantId" data-product-id="{{ $product->id }}" value="{{ $defaultVariant?->id }}">
                            <button type="button" 
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-product-stock="{{ $currentStock }}"
                                    class="add-to-cart-btn w-full bg-green-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center group">
                                <i class="fas fa-shopping-cart mr-3 group-hover:rotate-12 transition-transform"></i>
                                Ajouter au panier
                            </button>

                            <form action="{{ route('checkout.direct', $product->id) }}" method="GET" id="buyNowForm">
                                <input type="hidden" name="quantity" id="buyNowQuantity" value="1">
                                    <input type="hidden" name="variant_id" class="selectedVariantInput" value="{{ $defaultVariant?->id }}">
                                <button type="submit" 
                                        class="buy-now-btn w-full bg-gray-900 text-white py-4 rounded-xl font-bold text-lg hover:bg-gray-800 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center group">
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
                                    <input type="hidden" name="variant_id" class="selectedVariantInput" value="{{ $defaultVariant?->id }}">
                                <button type="submit"
                                        class="buy-now-btn w-full bg-gray-900 text-white py-3 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all duration-300 shadow flex items-center justify-center">
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
            <span id="modalImageInfo">1 / {{ $galleryImageUrls->count() }}</span>
        </div>
        
        <!-- Thumbnails in Modal -->
        @if($galleryImages->count() > 1)
            <div class="absolute bottom-20 left-1/2 transform -translate-x-1/2 flex space-x-2 overflow-x-auto max-w-full p-2">
                @foreach($galleryImages as $index => $image)
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

<script>
// Gallery functionality
let currentImageIndex = 1;
const images = @json($galleryImageUrls->values());
const totalImages = images.length;

// Change main image
function changeMainImage(src, index) {
    const mainImage = document.getElementById('mainImage');
    
    // Add fade out effect
    mainImage.style.opacity = '0';
    
    setTimeout(() => {
        mainImage.src = src;
        mainImage.style.opacity = '1';
        syncGallerySelection(index);
    }, 200);
}

// Update active thumbnail indicator
function updateActiveThumbnail(index) {
    // Remove all active indicators
    document.querySelectorAll('#thumbnailsContainer button').forEach(btn => {
        const thumbnail = btn.querySelector('.relative');
        thumbnail.classList.remove('border-emerald-500');
        thumbnail.classList.add('border-gray-200');
        const checkIcon = btn.querySelector('.bg-emerald-500');
        if (checkIcon) {
            checkIcon.remove();
        }
    });
    
    // Add active indicator to current thumbnail
    const currentThumbnail = document.querySelector(`#thumbnailsContainer button:nth-child(${index})`);
    if (currentThumbnail) {
        const thumbnailDiv = currentThumbnail.querySelector('.relative');
        thumbnailDiv.classList.remove('border-gray-200');
        thumbnailDiv.classList.add('border-emerald-500');
        
        const checkIcon = document.createElement('div');
        checkIcon.className = 'absolute top-2 right-2 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center';
        checkIcon.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
        thumbnailDiv.appendChild(checkIcon);
    }
}


function syncGallerySelection(index) {
    currentImageIndex = index;
    const currentIndex = document.getElementById('currentImageIndex');
    if (currentIndex && index > 0) currentIndex.textContent = index;
    updateActiveThumbnail(index);
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
    
    const galleryImage = currentImageIndex > 0 ? images[currentImageIndex - 1] : null;
    modalImage.src = galleryImage || document.getElementById('mainImage').src;
    modalImageInfo.textContent = currentImageIndex > 0 ? `${currentImageIndex} / ${totalImages}` : '';
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
        document.getElementById('mainImage').src = images[newIndex - 1];
        modalImageInfo.textContent = `${newIndex} / ${totalImages}`;
        syncGallerySelection(newIndex);
    }, 200);
}

function changeModalImage(src, index) {
    const modalImage = document.getElementById('modalImage');
    const modalImageInfo = document.getElementById('modalImageInfo');
    
    modalImage.style.opacity = '0';
    setTimeout(() => {
        modalImage.src = src;
        modalImage.style.opacity = '1';
        document.getElementById('mainImage').src = src;
        modalImageInfo.textContent = `${index} / ${totalImages}`;
        syncGallerySelection(index);
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
    newValue = Math.max(1, Math.min(newValue, parseInt(input.max || 1)));
    
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


const variantChooser = document.getElementById('variantChooser');
if (variantChooser) {
    const variants = JSON.parse(variantChooser.dataset.variants || '[]');
    const selected = {};
    const selectedVariantId = document.getElementById('selectedVariantId');
    const quantityInput = document.getElementById('quantity');
    const mainImage = document.getElementById('mainImage');
    const basePrice = document.getElementById('variantBasePrice');
    const finalPrice = document.getElementById('variantFinalPrice');
    const sku = document.getElementById('variantSku');
    const unit = document.getElementById('variantUnit');
    const message = document.getElementById('variantMessage');
    const stockDot = document.getElementById('variantStockDot');
    const stockLabel = document.getElementById('variantStockLabel');
    const stockCount = document.getElementById('variantStockCount');
    const stockDetails = document.getElementById('variantStockDetails');
    const addButton = document.querySelector('.add-to-cart-btn[data-product-id="{{ $product->id }}"]');
    const buyNowButtons = document.querySelectorAll('.buy-now-btn');
    const defaultVariant = variants.find(v => String(v.id) === String(variantChooser.dataset.defaultId)) || variants[0];

    function matchingVariant() {
        const attributeIds = [...variantChooser.querySelectorAll('[data-attribute]')].map(el => el.dataset.attribute);
        if (!attributeIds.every(id => selected[id])) return null;
        return variants.find(variant => attributeIds.every(id => String(variant.values[id]) === String(selected[id])));
    }

    function possible(attributeId, valueId) {
        return variants.some(variant => {
            if (variant.stock_quantity < 1) return false;
            return Object.entries(selected).every(([attr, val]) => attr === String(attributeId) || String(variant.values[attr]) === String(val))
                && String(variant.values[attributeId]) === String(valueId);
        });
    }

    function setPurchaseAvailability(isAvailable) {
        if (addButton) {
            addButton.disabled = !isAvailable;
            addButton.classList.toggle('opacity-50', !isAvailable);
            addButton.classList.toggle('cursor-not-allowed', !isAvailable);
        }
        buyNowButtons.forEach(button => {
            button.disabled = !isAvailable;
            button.classList.toggle('opacity-50', !isAvailable);
            button.classList.toggle('cursor-not-allowed', !isAvailable);
        });
    }

    function updateStockDisplay(stock) {
        const isAvailable = stock > 0;
        if (stockDot) {
            stockDot.classList.toggle('bg-emerald-500', isAvailable);
            stockDot.classList.toggle('animate-pulse', isAvailable);
            stockDot.classList.toggle('bg-red-500', !isAvailable);
        }
        if (stockLabel) {
            stockLabel.textContent = isAvailable ? 'Disponible en stock' : 'Rupture de stock';
            stockLabel.classList.toggle('text-emerald-700', isAvailable);
            stockLabel.classList.toggle('text-red-700', !isAvailable);
        }
        if (stockCount) {
            stockCount.textContent = `${stock} unités disponibles`;
            stockCount.classList.toggle('hidden', !isAvailable);
        }
        stockDetails?.classList.toggle('hidden', !isAvailable);
        setPurchaseAvailability(isAvailable);
    }

    function applyVariant(variant) {
        if (!variant) {
            selectedVariantId.value = '';
            document.querySelectorAll('.selectedVariantInput').forEach(input => input.value = '');
            setPurchaseAvailability(false);
            if (message) message.classList.remove('hidden');
            return;
        }

        selectedVariantId.value = variant.id;
        document.querySelectorAll('.selectedVariantInput').forEach(input => input.value = variant.id);
        if (basePrice) basePrice.textContent = Number(variant.price).toFixed(2);
        if (finalPrice) finalPrice.textContent = Number(variant.final_price).toFixed(2);
        if (sku) sku.textContent = variant.sku || '';
        if (unit) unit.textContent = variant.unit || '';
        if (quantityInput) {
            quantityInput.max = variant.stock_quantity;
            quantityInput.value = Math.min(Number(quantityInput.value || 1), Math.max(1, variant.stock_quantity));
            updateQuantity(0);
        }
        if (variant.image && mainImage) {
            mainImage.src = variant.image;
            const galleryIndex = images.indexOf(variant.image);
            syncGallerySelection(galleryIndex >= 0 ? galleryIndex + 1 : 0);
        }
        updateStockDisplay(variant.stock_quantity);
        if (message) message.classList.toggle('hidden', variant.stock_quantity > 0);
    }

    function refreshOptions() {
        document.querySelectorAll('.variant-option').forEach(button => {
            const isSelected = String(selected[button.dataset.attributeId]) === String(button.dataset.valueId);
            button.classList.toggle('bg-emerald-600', isSelected);
            button.classList.toggle('text-white', isSelected);
            button.classList.toggle('border-emerald-600', isSelected);
            const enabled = possible(button.dataset.attributeId, button.dataset.valueId) || isSelected;
            button.disabled = !enabled;
            button.classList.toggle('opacity-40', !enabled);
            button.classList.toggle('cursor-not-allowed', !enabled);
        });
    }

    document.querySelectorAll('.variant-option').forEach(button => {
        button.addEventListener('click', () => {
            if (button.disabled) return;
            selected[button.dataset.attributeId] = button.dataset.valueId;
            refreshOptions();
            applyVariant(matchingVariant());
        });
    });


    document.querySelectorAll('#buyNowForm, #fixedBuyNowForm').forEach(form => {
        form.addEventListener('submit', event => {
            if (!selectedVariantId.value) {
                event.preventDefault();
                if (message) message.classList.remove('hidden');
                variantChooser.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        });
    });

    if (defaultVariant) {
        Object.entries(defaultVariant.values).forEach(([attributeId, valueId]) => selected[attributeId] = valueId);
        refreshOptions();
        applyVariant(defaultVariant);
    }
}

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