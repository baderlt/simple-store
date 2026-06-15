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
                        @php
                            $displayStock = $product->usesVariants()
                                ? ($product->variants->sum('stock_quantity'))
                                : $product->stock_quantity;
                        @endphp
                        @if($displayStock <= 5 && $displayStock > 0)
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-amber-500/90 to-transparent text-white p-3 text-center">
                                <div class="flex items-center justify-center space-x-2 text-sm font-semibold">
                                    <i class="fas fa-bolt"></i>
                                    <span>Plus que {{ $displayStock }} en stock</span>
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
                            @php
                                $displayPrice = $product->usesVariants()
                                    ? $product->getCurrentPrice($product->defaultVariant ?? $product->variants->first())
                                    : $product->price;
                                $hasVariantDiscount = $product->usesVariants() ? false : $product->hasDiscount();
                            @endphp
                            @if($hasVariantDiscount)
                                <div class="flex">
                                    <span class="text-red-400 text-xs line-through mr-2">{{ format_price($product->price) }} DH</span>
                                    <span class="text-xl font-bold text-gray-900">{{ format_price($product->final_price) }} DH</span>
                                </div>
                            @elseif($product->usesVariants())
                                @php
                                    $defaultVariant = $product->defaultVariant ?? $product->variants->first();
                                    $variantDiscount = $defaultVariant ? $product->getDiscountedPrice($defaultVariant->price) : $displayPrice;
                                @endphp
                                @if($variantDiscount < $displayPrice)
                                    <div class="flex">
                                        <span class="text-red-400 text-xs line-through mr-2">{{ format_price($displayPrice) }} DH</span>
                                        <span class="text-xl font-bold text-gray-900">{{ format_price($variantDiscount) }} DH</span>
                                    </div>
                                @else
                                    <div class="text-xl font-bold text-gray-900">{{ format_price($displayPrice) }} DH</div>
                                @endif
                            @else
                                <div class="text-xl font-bold text-gray-900">{{ format_price($product->price) }} DH</div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <!-- View Details -->
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="flex-1 text-center bg-gray-100 text-gray-700 hover:bg-gray-200 py-2 rounded-xl font-medium text-sm transition-colors duration-300">
                                <i class="fas fa-eye mr-2"></i>Détails
                            </a>

                            <!-- Add to Cart / Select Options -->
                            @if($product->usesVariants())
                                <!-- Product with variants - go to product page to select -->
                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="w-10 h-10 bg-orange-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center"
                                   title="Sélectionner les options" style="background-color: rgb(238, 164, 26)">
                                    <i class="fas fa-cog"></i>
                                </a>
                            @elseif($product->stock_quantity > 0)
                                <button type="button"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-product-stock="{{ $product->stock_quantity }}"
                                    class="add-to-cart-btn w-10 h-10 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center group/btn">
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
