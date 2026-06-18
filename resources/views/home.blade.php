{{-- resources/views/home.blade.php - FIXED VERSION --}}
@extends('layouts.app')

@section('title', settings('seo_title', settings('store_name', 'Simple Store')))

@section('content')
    {{-- Hero Banner from Admin --}}
    @if(isset($activeBanners['hero']) && count($activeBanners['hero']) > 0)
        <section class="relative overflow-hidden">
            <div class="swiper hero-swiper">
                <div class="swiper-wrapper">
                    @foreach($activeBanners['hero'] as $banner)
                        <div class="swiper-slide">
                            <div class="relative h-[500px] md:h-[600px]">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                     alt="{{ $banner->title }}" 
                                     loading="lazy"
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-black/30"></div>
                                <div class="absolute inset-0 flex items-center">
                                    <div class="container mx-auto px-4 md:px-8">
                                        <div class="max-w-2xl">
                                            @if($banner->title)
                                                <h1 class="text-4xl md:text-6xl font-bold text-white mb-4 animate-fade-in-up">
                                                    {{ $banner->title }}
                                                </h1>
                                            @endif
                                            @if($banner->description)
                                                <p class="text-xl md:text-2xl text-white/90 mb-8 animate-fade-in-up animation-delay-200">
                                                    {{ $banner->description }}
                                                </p>
                                            @endif
                                            @if($banner->cta_text && $banner->cta_link)
                                                <a href="{{ $banner->cta_link }}" 
                                                   class="inline-block bg-white text-amber-800 hover:bg-amber-50 px-8 py-4 rounded-full font-semibold text-lg transition-all duration-300 transform hover:scale-105 animate-fade-in-up animation-delay-400 shadow-lg">
                                                    {{ $banner->cta_text }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </section>
    @else
        {{-- Default Hero Section --}}
        <section class="relative bg-gradient-to-br from-amber-700 via-yellow-600 to-orange-700 text-white py-20 md:py-28 overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden">
                <div class="absolute top-10 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-amber-900/20 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-yellow-300/10 rounded-full blur-3xl"></div>
            </div>
            
            <div class="container mx-auto px-4 relative z-10">
                <div class="max-w-3xl mx-auto text-center">
                    <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-semibold mb-6 animate-pulse">
                        <i class="fas fa-star mr-2"></i> {{ __('messages.trusted_store') }}
                    </span>
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                        {{ settings('hero_title_prefix', 'Saveurs') }} <span class="text-yellow-300">{{ settings('hero_title_emphasis', 'authentiques') }}</span> {{ settings('hero_title_suffix', '& senteurs raffinées') }}
                    </h1>
                    <p class="text-xl md:text-2xl mb-10 text-white/90 max-w-2xl mx-auto">
                        {{ settings('hero_subtitle', __('messages.hero_subtitle_default')) }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('products.index') }}" 
                           class="group bg-white text-amber-800 hover:bg-amber-50 px-8 py-4 rounded-full font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-basket-shopping group-hover:rotate-12 transition-transform"></i>
                            {{ __('messages.discover_products') }}
                        </a>
                        <a href="#categories" 
                           class="group bg-transparent border-2 border-white/50 text-white hover:bg-white/10 px-8 py-4 rounded-full font-semibold text-lg transition-all duration-300 backdrop-blur-sm flex items-center justify-center gap-2">
                            <i class="fas fa-tags group-hover:scale-110 transition-transform"></i>
                            {{ __('messages.explore_categories') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <a href="#categories" class="text-white/70 hover:text-white transition-colors">
                    <i class="fas fa-chevron-down text-3xl"></i>
                </a>
            </div>
        </section>
    @endif

    {{-- CATEGORIES SECTION WITH SWIPER --}}
    <section id="categories" class="py-4 md:py-20 bg-gradient-to-b from-white to-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <i class="fas fa-tags mr-2"></i> {{ __('messages.our_products') }}
                </span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4">
                    {{ __('messages.explore_by_category') }}
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    {{ __('messages.category_subtitle') }}
                </p>
            </div>
            
            @if($categories->count() > 0)
                <!-- Categories Swiper -->
                <div class="relative">
                    <div class="swiper categories-swiper pb-6">
                        <div class="swiper-wrapper">
                            @foreach($categories as $category)
                                @php
                                    $icons = [
                                        'miel' => 'jar', 'thé' => 'mug-hot', 'parfum' => 'spray-can-sparkles',
                                        'beauté' => 'spa', 'bébé' => 'baby', 'hygiène' => 'hands-wash',
                                        'bio' => 'leaf', 'naturel' => 'seedling', 'cadeau' => 'gift',
                                        'gourmand' => 'cookie-bite', 'épicerie' => 'bottle-droplet',
                                        'coffret' => 'gift', 'huile' => 'bottle-droplet', 'savon' => 'soap',
                                    ];
                                    
                                    $icon = 'basket-shopping';
                                    foreach($icons as $key => $value) {
                                        if(stripos($category->name, $key) !== false) {
                                            $icon = $value;
                                            break;
                                        }
                                    }
                                    
                                    $colorSchemes = [
                                        ['bg' => 'from-green-50 to-emerald-50', 'text' => 'text-green-700', 'border' => 'border-green-200', 'hover' => 'hover:border-green-400', 'icon' => 'from-green-500 to-emerald-500'],
                                        ['bg' => 'from-blue-50 to-cyan-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'hover' => 'hover:border-blue-400', 'icon' => 'from-blue-500 to-cyan-500'],
                                        ['bg' => 'from-purple-50 to-pink-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'hover' => 'hover:border-purple-400', 'icon' => 'from-purple-500 to-pink-500'],
                                        ['bg' => 'from-orange-50 to-amber-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'hover' => 'hover:border-orange-400', 'icon' => 'from-orange-500 to-amber-500'],
                                        ['bg' => 'from-red-50 to-rose-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'hover' => 'hover:border-red-400', 'icon' => 'from-red-500 to-rose-500'],
                                        ['bg' => 'from-teal-50 to-emerald-50', 'text' => 'text-teal-700', 'border' => 'border-teal-200', 'hover' => 'hover:border-teal-400', 'icon' => 'from-teal-500 to-emerald-500'],
                                        ['bg' => 'from-indigo-50 to-violet-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'hover' => 'hover:border-indigo-400', 'icon' => 'from-indigo-500 to-violet-500'],
                                    ];
                                    $colors = $colorSchemes[$loop->index % count($colorSchemes)];
                                @endphp
                                
                                <div class="swiper-slide">
                                    <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                                       class="group block rounded-xl md:rounded-2xl bg-gradient-to-br {{ $colors['bg'] }} border {{ $colors['border'] }} {{ $colors['hover'] }} transition-all duration-500 hover:shadow-lg hover:-translate-y-1 overflow-hidden h-full">
                                        
                                        <!-- Image Container -->
                                        <div class="relative h-40 md:h-48 lg:h-56 overflow-hidden">
                                            @if($category->image)
                                                <img src="{{ asset('storage/' . $category->image) }}" 
                                                     alt="{{ $category->name }}"
                                                     loading="lazy"   
                                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent"></div>
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br {{ $colors['icon'] }}">
                                                    <div class="relative">
                                                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl"></div>
                                                        <i class="fas fa-{{ $icon }} relative z-10 text-4xl md:text-5xl text-white"></i>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Product Count Badge -->
                                            @if($category->products_count > 0)
                                                <div class="absolute top-3 md:top-4 right-3 md:right-4">
                                                    <span class="bg-white/90 backdrop-blur-sm {{ $colors['text'] }} text-xs px-2 py-1 rounded-full font-bold shadow">
                                                        {{ $category->products_count }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Text Content -->
                                        <div class="p-4 md:p-5 text-center">
                                            <h3 class="font-bold {{ $colors['text'] }} text-base md:text-lg mb-2 group-hover:text-gray-900 transition-colors duration-300 line-clamp-2">
                                                {{ $category->name }}
                                            </h3>
                                            
                                            <div class="inline-flex items-center gap-1 text-sm font-medium {{ $colors['text'] }} group-hover:text-gray-800 transition-colors duration-300">
                                                <span>{{ __('messages.see_products') }}</span>
                                                <i class="fas fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform duration-300"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Navigation Buttons -->
                    <div class="swiper-button-next categories-next"></div>
                    <div class="swiper-button-prev categories-prev"></div>
                    
                    <!-- Pagination -->
                    <div class="swiper-pagination categories-pagination"></div>
                </div>
            @else
                <div class="text-center py-4">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-inbox text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('messages.no_category') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('messages.categories_soon') }}</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Mid-page Banner --}}
    @if(isset($activeBanners['middle']) && count($activeBanners['middle']) > 0)
        @foreach($activeBanners['middle'] as $banner)
            <section class="py-2 md:py-4">
                <div class="container mx-auto px-4">
                    <div class="relative rounded-2xl overflow-hidden shadow-xl">
                        <img src="{{ asset('storage/' . $banner->image_path) }}" 
                             alt="{{ $banner->title }}" 
                             loading="lazy"  
                             class="w-full h-64 md:h-80 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent"></div>
                        <div class="absolute inset-0 flex items-center">
                            <div class="px-8 md:px-12 max-w-xl">
                                @if($banner->title)
                                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                                        {{ $banner->title }}
                                    </h2>
                                @endif
                                @if($banner->description)
                                    <p class="text-lg text-gray-200 mb-6">
                                        {{ $banner->description }}
                                    </p>
                                @endif
                                @if($banner->cta_text && $banner->cta_link)
                                    <a href="{{ $banner->cta_link }}" 
                                       class="inline-block bg-white text-green-700 hover:bg-green-50 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                                        {{ $banner->cta_text }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach
    @endif

    {{-- Featured Products --}}
    <section class="py-4 md:py-6 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <i class="fas fa-star mr-2"></i> {{ __('home.featured_title') }}
                </span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4">
                    {{ __('home.featured_products') }}
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    {{ __('home.featured_description') }}
                </p>
            </div>
            
      @if($featuredProducts->count() > 0)
    <div class="mb-6 md:mb-8">
        <!-- Products Grid -->
        <div class="products-grid grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-6 items-stretch">
            @foreach($featuredProducts as $product)
                <div class="product-grid-item flex h-full min-w-0">
                    @include('products._card', ['product' => $product])
                </div>
            @endforeach
        </div>

        <!-- Toast Notification -->
        <div id="cart-toast" class="fixed top-5 right-5 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden transform translate-x-full transition-transform duration-300">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <span id="toast-message">{{ __('notifications.cart.added') }}</span>
            </div>
        </div>

        <!-- Error Toast -->
        <div id="error-toast" class="fixed top-5 right-5 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden transform translate-x-full transition-transform duration-300">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span id="error-message">Erreur</span>
            </div>
        </div>

        <!-- View All Button -->
        <div class="text-center mt-8">
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center group relative overflow-hidden bg-gradient-to-r from-gray-900 to-black text-white px-8 py-4 rounded-full font-semibold text-lg hover:shadow-2xl transition-all duration-300">
                <span class="relative z-10 flex items-center">
                    Explorer tous les produits
                    <i class="fas fa-arrow-right ml-3 group-hover:translate-x-2 transition-transform"></i>
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-teal-600 transform -translate-x-full group-hover:translate-x-0 transition-transform duration-500"></div>
            </a>
        </div>
    </div>
@else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-box-open text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('home.none_featured') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('home.discover_available') }}</p>
                    <a href="{{ route('products.index') }}"
                       class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors duration-300">
                        {{ __('home.explore_products') }}
                    </a>
                </div>
            @endif
        </div>
    </section>



<script>
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(event) {
        const openButton = event.target.closest('[data-variant-modal-open]');
        const closeButton = event.target.closest('[data-variant-modal-close]');
        const optionButton = event.target.closest('[data-card-variant-option]');
        const quantityButton = event.target.closest('[data-card-quantity-change]');

        if (openButton) {
            event.preventDefault();
            const card = openButton.closest('[data-product-card]');
            const modal = card?.querySelector('[data-variant-modal]');
            document.querySelectorAll('[data-variant-modal]').forEach((item) => {
                if (item !== modal) item.classList.add('hidden');
            });
            modal?.classList.toggle('hidden');
            return;
        }

        if (closeButton) {
            event.preventDefault();
            closeButton.closest('[data-variant-modal]')?.classList.add('hidden');
            return;
        }

        if (quantityButton) {
            event.preventDefault();
            const card = quantityButton.closest('[data-product-card]');
            const addButton = card.querySelector('.product-card-add-btn');
            const value = card.querySelector('[data-card-quantity-value]');
            const unit = card.querySelector('[data-card-quantity-unit]');
            const selectedOption = card.querySelector('[data-card-variant-option].border-emerald-500');
            const min = Number(selectedOption?.dataset.minimum || 1);
            const max = Math.max(min, Number(selectedOption?.dataset.stock || 999));
            const step = Number(quantityButton.dataset.cardQuantityChange);
            const next = Math.min(max, Math.max(min, Number(value.textContent || min) + step));

            value.textContent = next;
            addButton.dataset.quantity = next;
            if (unit) unit.textContent = selectedOption?.dataset.unit || '';
            return;
        }

        if (optionButton) {
            event.preventDefault();
            const card = optionButton.closest('[data-product-card]');
            const finalPrice = card.querySelector('[data-card-final-price]');
            const basePrice = card.querySelector('[data-card-base-price]');
            const image = card.querySelector('[data-product-card-image]');
            const addButton = card.querySelector('.product-card-add-btn');
            const label = card.querySelector('[data-card-button-label]');
            const quantityPanel = card.querySelector('[data-card-quantity-panel]');
            const quantityValue = card.querySelector('[data-card-quantity-value]');
            const quantityUnit = card.querySelector('[data-card-quantity-unit]');
            const minimumMessage = card.querySelector('[data-card-minimum-message]');
            const minimumQuantity = Number(optionButton.dataset.minimum || 1);
            const quantityUnitText = optionButton.dataset.unit || '';
            const hasDiscount = Number(optionButton.dataset.rawFinalPrice) < Number(optionButton.dataset.rawPrice);

            finalPrice.textContent = `${optionButton.dataset.finalPrice} DH`;
            basePrice.textContent = `${optionButton.dataset.price} DH`;
            basePrice.classList.toggle('hidden', !hasDiscount);
            if (image && optionButton.dataset.image) image.src = optionButton.dataset.image;

            card.querySelectorAll('[data-card-variant-option]').forEach((button) => {
                button.classList.remove('border-emerald-500', 'bg-emerald-50', 'ring-2', 'ring-emerald-100');
            });
            optionButton.classList.add('border-emerald-500', 'bg-emerald-50', 'ring-2', 'ring-emerald-100');

            addButton.dataset.variantId = optionButton.dataset.variantId;
            addButton.dataset.productStock = optionButton.dataset.stock;
            addButton.dataset.quantity = minimumQuantity;
            const showQuantityCounter = minimumQuantity > 1;
            if (quantityPanel) quantityPanel.classList.toggle('hidden', !showQuantityCounter);
            if (quantityValue) quantityValue.textContent = minimumQuantity;
            if (quantityUnit) quantityUnit.textContent = quantityUnitText;
            if (minimumMessage) {
                minimumMessage.textContent = showQuantityCounter ? `{{ __('products.minimum_quantity_notice') }}`.replace(':quantity', `${minimumQuantity}${quantityUnitText}`) : '';
                minimumMessage.classList.toggle('hidden', !showQuantityCounter);
            }
            addButton.disabled = false;
            addButton.removeAttribute('data-variant-modal-open');
            addButton.classList.add('add-to-cart-btn', 'bg-green-600');
            addButton.querySelector('i').className = 'fas fa-box-open';
            label.textContent = '{{ __('products.add_to_pack') }}';
            if (!showQuantityCounter) {
                card.querySelector('[data-variant-modal]')?.classList.add('hidden');
            }
        }
    });

    document.addEventListener('click', function(event) {
        if (!event.target.closest('[data-product-card]')) {
            document.querySelectorAll('[data-variant-modal]').forEach((modal) => modal.classList.add('hidden'));
        }
    });

});
</script>

    {{-- Call to Action Banner --}}
    @if(isset($activeBanners['bottom']) && count($activeBanners['bottom']) > 0)
        @foreach($activeBanners['bottom'] as $banner)
            <section class="py-2 md:py-4">
                <div class="container mx-auto px-4">
                    <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-3xl overflow-hidden shadow-2xl">
                        <div class="grid md:grid-cols-2 items-center">
                            <div class="p-8 md:p-12">
                                @if($banner->title)
                                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                                        {{ $banner->title }}
                                    </h2>
                                @endif
                                @if($banner->description)
                                    <p class="text-lg text-gray-100 mb-8">
                                        {{ $banner->description }}
                                    </p>
                                @endif
                                @if($banner->cta_text && $banner->cta_link)
                                    <a href="{{ $banner->cta_link }}" 
                                       class="inline-flex items-center gap-2 bg-white text-green-700 hover:bg-gray-100 px-6 py-3 rounded-full font-semibold text-lg transition-all duration-300 transform hover:scale-105">
                                        <span>{{ $banner->cta_text }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                            <div class="h-64 md:h-auto">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                     alt="{{ $banner->title }}" 
                                     loading="lazy"  
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach
    @endif

    {{-- MAP & LOCATION SECTION --}}
    <section class="py-16 md:py-20 bg-gradient-to-br from-gray-900 to-gray-800 text-white">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Column: Information -->
                <div>
                    <span class="inline-block bg-green-500/20 text-green-300 px-4 py-2 rounded-full text-sm font-semibold mb-4 backdrop-blur-sm">
                        <i class="fas fa-map-marker-alt mr-2"></i> {{ __('home.location.title') }}
                    </span>
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                        {{ __('home.location.visit') }} <span class="text-amber-400">{{ settings('store_name', 'Maison Dorée') }}</span>
                    </h2>
                    
                    <div class="space-y-6 mb-8">
                        <!-- Address -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-pin text-xl text-green-400"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl mb-2">{{ __('home.location.address') }}</h3>
                                <p class="text-gray-300">
                            {{settings("address")}}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Hours -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-amber-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-xl text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl mb-2">{{ __('home.location.hours') }}</h3>
@php
    $workingHoursParts = collect(preg_split('/[|\\\\,\/]+/', (string) settings('working_hours')))
        ->map(function ($part) {
            [$days, $hours] = array_pad(array_map('trim', explode(':', $part, 2)), 2, '');

            return compact('days', 'hours');
        })
        ->filter(fn ($part) => $part['days'] !== '' || $part['hours'] !== '')
        ->values();
@endphp

<div class="space-y-1 text-gray-300">
    @foreach($workingHoursParts as $part)
        <div class="flex justify-between">
            <span>{{ $part['days'] }}:&nbsp;</span>
            <span class="font-semibold">{{ $part['hours'] }}</span>
        </div>
    @endforeach
</div>

                            </div>
                        </div>
                        
                        <!-- Contact -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone-alt text-xl text-purple-400"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl mb-2">{{ __('home.location.contact') }}</h3>
                                <div class="space-y-2 text-gray-300">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-phone text-green-400"></i>
                                    @php
                                        $phoneRaw = settings('phone');
                                        // keep only numbers and +
                                        $phoneLink = preg_replace('/[^0-9+]/', '', $phoneRaw);
                                    @endphp

                                    <a href="tel:{{ $phoneLink }}"
                                    class="hover:text-white transition-colors">
                                        {{ $phoneRaw }}
                                    </a>

                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-envelope text-green-400"></i>
                                       <a href="mailto:{{ settings('email') }}"
                                      class="hover:text-white transition-colors">
                                           {{ settings('email') }}
                                         </a>

                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fab fa-whatsapp text-green-400"></i>
                                        @php
                                        // remove spaces, +, and non-numeric chars
                                            $whatsapp = preg_replace('/\D+/', '', settings('whatsapp'));
                                        @endphp

                                        <a href="https://wa.me/{{ $whatsapp }}"
                                        target="_blank"
                                        class="hover:text-white transition-colors">
                                            WhatsApp: {{ settings('whatsapp') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Directions Button -->
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ settings('maps_link') }}"
                           target="_blank"
                           class="group bg-green-600 hover:bg-green-700 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center gap-2">
                            <i class="fas fa-directions"></i>
                            <span>{{ __('home.location.directions') }}</span>
                        </a>
                        <a href="tel:{{ $phoneLink }}"
                           class="group bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-2">
                            <i class="fas fa-phone"></i>
                            <span>Nous appeler</span>
                        </a>
                    </div>
                </div>
                
                <!-- Right Column: Map -->
                <div class="relative rounded-2xl overflow-hidden shadow-2xl h-[400px] lg:h-[500px]">
                    <!-- Map Container -->
                    <div id="map" class="w-full h-full"></div>
                    
                    <!-- Map Overlay Info -->
                    <div class="absolute bottom-4 left-4 right-4 bg-black/80 backdrop-blur-sm rounded-lg p-4">
                        <div class="flex items-center justify-between">
                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-16 md:py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-4">
                    Pourquoi Nous <span class="text-green-600">{{ __('home.features.choose_title') }}</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    {{ __('home.features.choose_description') }}
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 hover:border-green-300 transition-all duration-300 group">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 text-white rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-seedling text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3">{{ __('home.features.expert_advice.title') }}</h3>
                    <p class="text-gray-600">{{ __('home.features.expert_advice.description') }}</p>
                </div>
                
                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-100 hover:border-blue-300 transition-all duration-300 group">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-500 text-white rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3">{{ __('home.features.certified_products.title') }}</h3>
                    <p class="text-gray-600">{{ __('home.features.certified_products.description') }}</p>
                </div>
                
                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-100 hover:border-purple-300 transition-all duration-300 group">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 text-white rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-truck-fast text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3">Livraison Express</h3>
                    <p class="text-gray-600">Livraison rapide à domicile dans toute la région en 24h maximum</p>
                </div>
                
                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100 hover:border-orange-300 transition-all duration-300 group">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-orange-500 to-amber-500 text-white rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-hand-holding-heart text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3">Service Personnalisé</h3>
                    <p class="text-gray-600">Accompagnement personnalisé pour choisir vos saveurs, senteurs et cadeaux</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Newsletter --}}
    {{-- <section class="py-16 bg-gradient-to-r from-green-600 to-teal-600 text-white">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Restez Informé</h2>
                <p class="text-xl mb-8 text-green-100">
                    Inscrivez-vous à notre newsletter pour recevoir nos offres spéciales et découvertes gourmandes
                </p>
                
                <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                    <input type="email" 
                           placeholder="Votre adresse email" 
                           class="flex-grow px-6 py-3 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <button type="submit" 
                            class="bg-white text-green-700 hover:bg-green-50 px-8 py-3 rounded-full font-semibold transition-colors duration-300">
                        S'abonner
                    </button>
                </form>
                
                <p class="text-sm text-green-200 mt-4">
                    <i class="fas fa-lock mr-1"></i> Vos données sont sécurisées. Pas de spam.
                </p>
            </div>
        </div>
    </section> --}}
@endsection


<link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="{{asset("css/home.css")}}" />


<script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// COMPLETE FIX FOR CATEGORIES SWIPER
document.addEventListener('DOMContentLoaded', function() {

    // 1. HERO BANNER SWIPER
    const heroSwiper = document.querySelector('.hero-swiper');
    if (heroSwiper && window.Swiper) {
        new window.Swiper('.hero-swiper', {
            modules: [window.SwiperNavigation, window.SwiperPagination, window.SwiperAutoplay],
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            speed: 800,
        });
    }
    
    // 2. CATEGORIES SWIPER - FIXED FOR DESKTOP
    const categoriesSwiper = document.querySelector('.categories-swiper');
    if (categoriesSwiper && window.Swiper) {
        new window.Swiper('.categories-swiper', {
            modules: [window.SwiperNavigation, window.SwiperPagination, window.SwiperAutoplay],
            slidesPerView: 2,
            slidesPerGroup: 1,
            spaceBetween: 16,
            loop: false,
            centeredSlides: false,
             centerInsufficientSlides: true,
            roundLengths: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            navigation: {
                nextEl: '.categories-next',
                prevEl: '.categories-prev',
            },
            pagination: {
                el: '.categories-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            breakpoints: {
                // Mobile Small
                320: {
                    slidesPerView: 2,
                    spaceBetween: 12,
                },
                // Mobile
                480: {
                    slidesPerView: 2,
                    spaceBetween: 16,
                },
                // Tablet Small
                640: {
                    slidesPerView: 3,
                    spaceBetween: 16,
                },
                // Tablet
                768: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
                // Desktop Small
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                },
                // Desktop
                1280: {
                    slidesPerView: 5,
                    spaceBetween: 24,
                },
                // Desktop Large
                1536: {
                    slidesPerView: 6,
                    spaceBetween: 24,
                },
            },
            speed: 600,
            grabCursor: true,
            observer: true,
            observeParents: true,
        });
        
    }
    
    // 3. LEAFLET MAP INITIALIZATION
    if (document.getElementById('map')) {
        initMap();
    }
    
    // 4. SMOOTH SCROLL FOR ANCHOR LINKS
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            e.preventDefault();
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
});

    const latitude  = {{ settings('latitude') }};
    const longitude = {{ settings('longitude') }};

    function initMap() {
        const casablanca = [latitude, longitude];
    
    const map = L.map('map').setView(casablanca, 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);
    
    const storeIcon = L.divIcon({
        html: '<div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center border-4 border-white shadow-lg"><i class="fas fa-store text-white text-lg"></i></div>',
        className: 'custom-div-icon',
        iconSize: [48, 48],
        iconAnchor: [24, 48],
        popupAnchor: [0, -48]
    });
        const store = {
        name: @json(settings('store_name', 'Maison Dorée')),
        address: @json(settings('address', 'Casablanca')),
        phone_raw: @json(settings('phone')),
        phone_link: "{{ preg_replace('/[^0-9+]/', '', settings('phone')) }}",
        email: @json(settings('email')),
        hours_label: @json(__('home.location.hours')),
        working_hours: @json($workingHoursParts),
        lat: {{ settings('latitude', 33.5731) }},
        lng: {{ settings('longitude', -7.5898) }},
    };
    const marker = L.marker(casablanca, { icon: storeIcon }).addTo(map);
    
    marker.bindPopup(`
        <div class="p-2 min-w-[220px]">
            <h3 class="font-bold text-lg text-gray-800">
                ${store.name}
            </h3>

            <p class="text-sm text-gray-600">
                ${store.address}
            </p>

            <div class="mt-2 space-y-1 text-sm">

                ${store.phone_raw ? `
                <div class="flex items-center gap-2">
                    <i class="fas fa-phone text-green-600"></i>
                    <a href="tel:${store.phone_link}" class="hover:underline">
                        ${store.phone_raw}
                    </a>
                </div>` : ''}

                ${store.email ? `
                <div class="flex items-center gap-2">
                    <i class="fas fa-envelope text-green-600"></i>
                    <a href="mailto:${store.email}" class="hover:underline">
                        ${store.email}
                    </a>
                </div>` : ''}

                ${store.working_hours.length ? `
                <div class="flex items-start gap-2">
                    <i class="fas fa-clock text-green-600 mt-1"></i>
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-gray-800">${store.hours_label}</div>
                        <div class="mt-1 space-y-0.5 text-gray-600">
                            ${store.working_hours.map(({ days, hours }) => `
                                <div class="flex justify-between gap-3" dir="auto">
                                    <span>${days}:</span>
                                    <span class="font-semibold whitespace-nowrap">${hours}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>` : ''}

            </div>

            <a href="https://www.google.com/maps/dir/?api=1&destination=${store.lat},${store.lng}"
               target="_blank"
               class="mt-3 inline-flex items-center justify-center gap-1 w-full
                      bg-green-600 text-white px-3 py-1.5 rounded text-sm
                      hover:bg-green-700 transition-colors">
                <i class="fas fa-directions"></i>
                Itinéraire
            </a>
        </div>
    `).openPopup();
    
    L.circle(casablanca, {
        color: '#10b981',
        fillColor: '#10b981',
        fillOpacity: 0.1,
        radius: 500
    }).addTo(map);
}
    
</script>
