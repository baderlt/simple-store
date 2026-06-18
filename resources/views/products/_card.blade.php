@php
    $activeVariants = $product->usesVariants()
        ? $product->variants->where('is_active', true)->filter(fn ($variant) => $variant->stock_quantity > 0)->values()
        : collect();
    $defaultVariant = $activeVariants->firstWhere('is_default', true) ?: $activeVariants->first();
    $displayStock = $product->usesVariants() ? $activeVariants->sum('stock_quantity') : $product->stock_quantity;
    $displayPrice = $product->usesVariants() && $defaultVariant ? (float) $defaultVariant->price : (float) $product->price;
    $displayFinalPrice = $product->getDiscountedPrice($displayPrice);
    $hasDisplayDiscount = $displayFinalPrice < $displayPrice;
    $productImageUrl = $product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : null;
    $variantPayload = $activeVariants->map(function ($variant) use ($product, $productImageUrl) {
        $attributes = $variant->option_values;
        $basePrice = (float) $variant->price;

        return [
            'id' => $variant->id,
            'label' => $attributes ? implode(' / ', array_values($attributes)) : ($variant->sku ?: __('products.variant')),
            'price' => $basePrice,
            'final_price' => (float) $product->getDiscountedPrice($basePrice),
            'stock_quantity' => (int) $variant->stock_quantity,
            'minimum_quantity' => $variant->minimumOrderQuantity(),
            'quantity_unit' => $variant->quantityUnit(),
            'image' => $variant->image_path ? asset('storage/' . $variant->image_path) : $productImageUrl,
        ];
    })->values();
@endphp

<div class="card-product group relative h-full w-full bg-gradient-to-br from-white to-gray-50 rounded-2xl border border-gray-100 hover:border-emerald-200 transition-all duration-300 hover:shadow-xl overflow-visible flex flex-col"
     data-product-card="{{ $product->id }}">
    @if($product->hasDiscount())
        <div class="absolute top-3 left-0 z-10">
            <div class="relative bg-gradient-to-r from-rose-500 to-pink-600 text-white py-1 px-1 rounded-r-lg shadow-lg">
                <span class="font-bold text-xs lg:text-sm">-{{ $product->activeDiscount->discount_percentage }}%</span>
            </div>
        </div>
    @endif

    <div class="product-card-media relative aspect-square w-full overflow-hidden bg-white">
        <a href="{{ route('products.show', $product->slug) }}" class="flex h-full w-full items-center justify-center">
            @if($productImageUrl)
                <img src="{{ $productImageUrl }}"
                     alt="{{ $product->name }}"
                     loading="lazy"
                     width="600"
                     height="600"
                     data-product-card-image
                     class="h-full w-full object-contain p-2 sm:p-3 group-hover:scale-105 transition-transform duration-700">
            @else
                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-gem text-gray-300 text-5xl mb-2"></i>
                        <p class="text-gray-400 text-sm">Image non disponible</p>
                    </div>
                </div>
            @endif
        </a>

        @if($displayStock <= 5 && $displayStock > 0)
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-amber-500/90 to-transparent text-white p-3 text-center">
                <div class="flex items-center justify-center space-x-2 text-sm font-semibold">
                    <i class="fas fa-bolt"></i>
                    <span>Plus que {{ $displayStock }} en stock</span>
                </div>
            </div>
        @endif
    </div>

    <div class="product-content p-3 md:p-5 flex flex-1 flex-col">
        <div class="mb-2 sm:mb-3 min-h-5">
            <a href="{{ route('products.index', ['category' => $product->category_id]) }}"
               class="inline-flex max-w-full items-center text-[10px] text-emerald-600 font-semibold uppercase tracking-wider hover:text-emerald-700">
                <i class="fas fa-tag mr-1.5"></i>
                <span class="truncate">{{ $product->category->name ?? 'Catégorie' }}</span>
            </a>
        </div>

        <h3 class="font-bold text-gray-900 text-sm sm:text-base mb-3 min-h-10 sm:min-h-12 line-clamp-2 group-hover:text-emerald-700 transition-colors leading-tight">
            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-emerald-700">{{ $product->name }}</a>
        </h3>

        <div class="product-card-price flex items-end justify-between mb-4 min-h-12">
            <div class="flex flex-wrap items-baseline gap-x-2">
                <span data-card-base-price class="{{ $hasDisplayDiscount ? '' : 'hidden' }} text-red-400 text-xs line-through whitespace-nowrap">{{ format_price($displayPrice) }} DH</span>
                <span data-card-final-price class="text-base sm:text-xl font-bold text-gray-900 whitespace-nowrap">{{ format_price($displayFinalPrice) }} DH</span>
            </div>
        </div>

        <div class="add-to-pack-button-wrapper mt-auto">
            @if($product->usesVariants())
                <button type="button"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-product-stock="{{ $displayStock }}"
                        data-variant-id=""
                        data-quantity="1"
                        data-variant-modal-open
                        class="add-to-pack-btn product-card-add-btn w-full bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center gap-2 py-2.5 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">
                    <i class="fas fa-box-open"></i>
                    <span data-card-button-label>{{ __('products.choose_quantity') }}</span>
                </button>
            @elseif($product->stock_quantity > 0)
                <button type="button"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-product-stock="{{ $product->stock_quantity }}"
                        class="add-to-pack-btn add-to-cart-btn w-full bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center gap-2 py-2.5 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm group/btn">
                    <i class="fas fa-box-open group-hover/btn:scale-110 transition-transform"></i>
                    <span>{{ __('products.add_to_pack') }}</span>
                </button>
            @else
                <button disabled class="add-to-pack-btn w-full bg-gray-200 text-gray-400 rounded-xl cursor-not-allowed flex items-center justify-center gap-2 py-2.5 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">
                    <i class="fas fa-box-open"></i>
                    <span>{{ __('products.add_to_pack') }}</span>
                </button>
            @endif
        </div>
    </div>

    @if($product->usesVariants() && $activeVariants->isNotEmpty())
        <div class="absolute inset-x-2 top-2 z-40 hidden max-h-[calc(100%-1rem)] overflow-y-auto rounded-2xl bg-white/95 p-2.5 shadow-2xl ring-1 ring-gray-100 backdrop-blur-md" data-variant-modal>
            <div class="mb-2 flex items-center justify-between gap-3">
                <p class="text-xs font-black uppercase tracking-wide text-emerald-600">{{ __('products.choose_variant') }}</p>
                <button type="button" data-variant-modal-close class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:text-gray-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="max-h-32 space-y-1 overflow-y-auto pr-1 sm:max-h-40">
                @foreach($variantPayload as $variant)
                    <button type="button"
                            class="flex w-full items-center justify-between gap-3 rounded-lg border border-gray-200 px-2.5 py-1.5 text-left transition hover:border-emerald-400 hover:bg-emerald-50"
                            data-card-variant-option
                            data-variant-id="{{ $variant['id'] }}"
                            data-price="{{ format_price($variant['price']) }}"
                            data-final-price="{{ format_price($variant['final_price']) }}"
                            data-raw-price="{{ $variant['price'] }}"
                            data-raw-final-price="{{ $variant['final_price'] }}"
                            data-image="{{ $variant['image'] }}"
                            data-minimum="{{ $variant['minimum_quantity'] }}"
                            data-unit="{{ $variant['quantity_unit'] }}"
                            data-stock="{{ $variant['stock_quantity'] }}">
                        <span class="min-w-0 truncate text-xs font-black text-gray-900">{{ $variant['label'] }}</span>
                        <span class="shrink-0 whitespace-nowrap text-xs font-black text-emerald-600">{{ format_price($variant['final_price']) }} DH</span>
                    </button>
                @endforeach
            </div>

            <div class="mt-2 hidden rounded-xl bg-emerald-50 p-2" data-card-quantity-panel>
                <div class="flex items-center justify-between gap-3">
                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-emerald-700 shadow-sm" data-card-quantity-change="-1">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <div class="text-center">
                        <div class="text-[11px] font-bold uppercase tracking-wide text-emerald-700">{{ __('products.choose_quantity') }}</div>
                        <div class="text-lg font-black text-gray-900"><span data-card-quantity-value>1</span><span data-card-quantity-unit></span></div>
                    </div>
                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-emerald-700 shadow-sm" data-card-quantity-change="1">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
                <p class="mt-1.5 hidden text-center text-[11px] font-bold text-emerald-700" data-card-minimum-message></p>
            </div>
        </div>
    @endif

    <div class="absolute inset-0 border-2 border-transparent group-hover:border-emerald-300 rounded-2xl transition-all duration-300 pointer-events-none"></div>
</div>
