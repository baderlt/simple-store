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
                                            {{ $category->products_count }}
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
        <div id="productsGrid" class="products-grid grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-6 items-stretch">
            @include('products._cards', ['products' => $products])
        </div>
        <div id="infiniteScrollStatus"
             class="flex min-h-24 items-center justify-center py-8 text-center text-gray-500"
             data-next-page-url="{{ $products->nextPageUrl() }}"
             aria-live="polite">
            @if($products->hasMorePages())
                <span id="infiniteScrollMessage">
                    <i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>
                    Chargement de plus de produits...
                </span>
            @else
                <span>Tous les produits sont affichés.</span>
            @endif
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

        const productsGrid = document.getElementById('productsGrid');
        const scrollStatus = document.getElementById('infiniteScrollStatus');

        if (!productsGrid || !scrollStatus || !scrollStatus.dataset.nextPageUrl) {
            return;
        }

        let nextPageUrl = scrollStatus.dataset.nextPageUrl;
        let isLoading = false;

        const observer = new IntersectionObserver(async entries => {
            if (!entries[0].isIntersecting || isLoading || !nextPageUrl) {
                return;
            }

            isLoading = true;

            try {
                const response = await fetch(nextPageUrl, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error(`Unable to load products (${response.status})`);
                }

                const page = await response.json();
                productsGrid.insertAdjacentHTML('beforeend', page.html);
                nextPageUrl = page.next_page_url;
                scrollStatus.dataset.nextPageUrl = nextPageUrl || '';

                if (!nextPageUrl) {
                    observer.disconnect();
                    scrollStatus.innerHTML = '<span>Tous les produits sont affichés.</span>';
                }
            } catch (error) {
                observer.disconnect();
                scrollStatus.innerHTML = '<button type="button" id="retryProducts" class="font-semibold text-emerald-600 hover:text-emerald-700">Le chargement a échoué. Réessayer</button>';
                document.getElementById('retryProducts').addEventListener('click', () => {
                    scrollStatus.innerHTML = '<span><i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>Chargement de plus de produits...</span>';
                    observer.observe(scrollStatus);
                }, { once: true });
            } finally {
                isLoading = false;
            }
        }, { rootMargin: '300px 0px' });

        observer.observe(scrollStatus);
    });
</script>
@endsection
