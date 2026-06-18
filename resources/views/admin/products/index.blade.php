@extends('admin.layouts.app')

@section('title', 'Gestion des Produits - Admin')
@section('header', 'Gestion des Produits')
@section('subheader', 'Liste et gestion de tous vos produits')

@section('content')
<div class="space-y-6">
    <!-- En-tête sticky compact -->
    <div id="stickyHeader" style="top: -40px" class="sticky top-0 z-40 bg-white border-b border-gray-200 -mx-4 sm:-mx-6 px-4 sm:px-6 transition-all duration-300 py-3">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <!-- Titre compact -->
            <div class="min-w-0 flex-1 sm:flex-none">
                <h1 class="text-xl font-bold text-gray-800 truncate">Produits</h1>
                <p class="text-sm text-gray-600 truncate hidden sm:block">Gérez votre catalogue</p>
            </div>

            <form id="adminProductSearchForm" method="GET" action="{{ route('admin.products.index') }}" class="w-full sm:w-auto sm:flex-1 flex flex-col sm:flex-row gap-2">
            @if(request('category_id'))
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            @endif
            <!-- Barre de recherche - Pleine largeur sur mobile -->
            <div class="w-full sm:w-auto sm:flex-1">
                <div class="relative">
                    <input type="text"
                           placeholder="{{ __('admin.search_database_placeholder') }}"
                           id="searchInput"
                           name="search"
                           value="{{ request('search') }}"
                           autocomplete="off"
                           class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white text-sm">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <!-- Bouton Filtres avec dropdown -->
                <div class="relative flex-1 sm:flex-none">
                    <button type="button" id="filterToggle" class="flex items-center justify-center gap-1 px-3 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 w-full sm:w-auto">
                        <i class="fas fa-filter text-gray-500"></i>
                        <span class="hidden sm:inline">{{ __('admin.filters') }}</span>
                        <span class="sm:hidden">{{ __('admin.filter') }}</span>
                    </button>

                    <!-- Dropdown Filtres -->
                    <div id="filterDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50 hidden p-4">
                        <div class="space-y-4">
                            <!-- Statut -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">{{ __('admin.status') }}</label>
                                <select id="statusFilter" name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                                    <option value="">{{ __('admin.all_statuses') }}</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('admin.active_plural') }}</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('admin.inactive_plural') }}</option>
                                </select>
                            </div>

                            <!-- Stock -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">{{ __('admin.stock') }}</label>
                                <select id="stockFilter" name="stock" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                                    <option value="">{{ __('admin.all_stock_levels') }}</option>
                                    <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>{{ __('admin.low_stock') }}</option>
                                    <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>{{ __('admin.out_of_stock') }}</option>
                                    <option value="sufficient" {{ request('stock') === 'sufficient' ? 'selected' : '' }}>{{ __('admin.sufficient_stock') }}</option>
                                </select>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-between pt-3 border-t">
                                <a href="{{ route('admin.products.index') }}" id="resetFilters" class="text-sm text-gray-600 hover:text-gray-900 px-3 py-1.5 rounded hover:bg-gray-100">
                                    <i class="fas fa-redo mr-1"></i>{{ __('admin.reset') }}
                                </a>
                                <button type="submit" id="applyFilters" class="text-sm bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700">
                                    {{ __('admin.apply') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bouton Ajouter -->
                <a href="{{ route('admin.products.create') }}"
                   class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 whitespace-nowrap w-full sm:w-auto">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline ml-1">{{ __('admin.add') }}</span>
                    <span class="sm:hidden ml-1">{{ __('admin.new') }}</span>
                </a>
            </div>
            </form>
        </div>

        <!-- Compteur et infos -->
        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
            <div class="flex items-center gap-3">
                <span>
                    <i class="fas fa-box mr-1"></i>
                    <span id="productCount">{{ $products->total() }}</span> produits
                </span>
                <span class="hidden sm:inline">
                    <i class="fas fa-filter mr-1"></i>
                    <span id="activeFiltersCount">0</span> actifs
                </span>
            </div>
            <div class="text-xs hidden sm:block">
                {{ __('admin.enter_to_search_database') }}
            </div>
        </div>
    </div>

    <!-- Statistiques compactes -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 pt-2">
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600">Total</p>
                    <p class="text-lg font-bold text-gray-800">{{ $products->total() }}</p>
                </div>
                <div class="bg-green-100 p-2 rounded-lg">
                    <i class="fas fa-boxes text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600">Rupture</p>
                    <p class="text-lg font-bold text-red-600">
                        {{ \App\Models\Product::where('stock_quantity', 0)->count() }}
                    </p>
                </div>
                <div class="bg-red-100 p-2 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600">Vedettes</p>
                    <p class="text-lg font-bold text-amber-600">
                        {{ \App\Models\Product::where('is_featured', true)->count() }}
                    </p>
                </div>
                <div class="bg-amber-100 p-2 rounded-lg">
                    <i class="fas fa-star text-amber-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600">Actifs</p>
                    <p class="text-lg font-bold text-blue-600">
                        {{ \App\Models\Product::where('is_active', true)->count() }}
                    </p>
                </div>
                <div class="bg-blue-100 p-2 rounded-lg">
                    <i class="fas fa-toggle-on text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des produits -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Tableau desktop -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catégorie</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prix</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 product-row" data-status="{{ $product->is_active ? 'active' : 'inactive' }}" data-stock="{{ $product->stock_quantity }}">
                            <!-- Colonne Produit -->
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 mr-3">
                                        @if($product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                                 alt="{{ $product->name }}"
                                                 class="h-10 w-10 rounded-lg object-cover border border-gray-200">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-box text-gray-400 text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate product-name">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500 truncate product-sku">{{ $product->sku ?: 'Non défini' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Colonne Catégorie -->
                            <td class="px-4 py-3">
                                @if($product->category)
                                    <span class="inline-flex items-center px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded">
                                        {{ Str::limit($product->category->name, 15) }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Colonne Prix -->
                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    @if($product->hasDiscount())
                                        <span class="text-sm font-bold text-green-600">{{ number_format($product->final_price, 2) }} DH</span>
                                        <span class="text-xs text-gray-400 line-through">{{ number_format($product->price, 2) }} DH</span>
                                    @else
                                        <span class="text-sm font-bold text-gray-900">{{ number_format($product->price, 2) }} DH</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Colonne Stock -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="product-stock text-sm {{ $product->isLowStock() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    @if($product->stock_quantity == 0)
                                        <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded">Rupture</span>
                                    @elseif($product->isLowStock())
                                        <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded">Faible</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Colonne Statut -->
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center text-xs {{ $product->is_active ? 'text-green-700' : 'text-gray-500' }}">
                                        <i class="fas fa-circle text-[6px] mr-1.5"></i>
                                        {{ $product->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                    @if($product->is_featured)
                                        <span class="text-xs text-amber-600">
                                            <i class="fas fa-star mr-1"></i>Vedette
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Colonne Actions -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.products.show', $product) }}"
                                       class="text-gray-400 hover:text-gray-600 p-1 hover:bg-gray-100 rounded"
                                       title="Voir">
                                        <i class="fas fa-eye w-4 h-4"></i>
                                    </a>

                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="text-blue-500 hover:text-blue-700 p-1 hover:bg-blue-50 rounded"
                                       title="Modifier">
                                        <i class="fas fa-edit w-4 h-4"></i>
                                    </a>

                                    <form action="{{ route('admin.products.destroy', $product) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirmDelete('{{ addslashes($product->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded"
                                                title="Supprimer">
                                            <i class="fas fa-trash w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-box-open text-gray-400"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
                                <a href="{{ route('admin.products.create') }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                    <i class="fas fa-plus mr-1"></i>
                                    Ajouter un produit
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Vue mobile compacte -->
        <div class="lg:hidden space-y-3 p-3">
            @forelse($products as $product)
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mobile-product-card" data-status="{{ $product->is_active ? 'active' : 'inactive' }}" data-stock="{{ $product->stock_quantity }}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start gap-3">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                     alt="{{ $product->name }}"
                                     class="h-12 w-12 rounded-lg object-cover border border-gray-200">
                            @else
                                <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400"></i>
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-semibold text-gray-900 truncate product-name">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5 product-sku">{{ $product->sku ?: 'Non défini' }}</p>
                            </div>
                        </div>
                        <span class="text-xs {{ $product->is_active ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $product->is_active ? '●' : '○' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Prix</p>
                            @if($product->hasDiscount())
                                <div>
                                    <span class="text-sm font-bold text-green-600">{{ number_format($product->final_price, 2) }} DH</span>
                                    <span class="text-xs text-gray-400 line-through block">{{ number_format($product->price, 2) }} DH</span>
                                </div>
                            @else
                                <span class="text-sm font-bold text-gray-900">{{ number_format($product->price, 2) }} DH</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Stock</p>
                            <div class="flex items-center gap-1">
                                <span class="product-stock text-sm {{ $product->isLowStock() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                    {{ $product->stock_quantity }}
                                </span>
                                @if($product->stock_quantity == 0)
                                    <span class="text-xs bg-red-100 text-red-800 px-1.5 py-0.5 rounded">Rupture</span>
                                @elseif($product->isLowStock())
                                    <span class="text-xs bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded">Faible</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.products.show', $product) }}"
                               class="text-xs text-gray-600 hover:text-gray-900">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>

                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="text-xs text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit mr-1"></i>Modifier
                            </a>
                        </div>

                        <form action="{{ route('admin.products.destroy', $product) }}"
                              method="POST"
                              onsubmit="return confirmDelete('{{ addslashes($product->name) }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-box-open text-gray-400"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
                    <a href="{{ route('admin.products.create') }}"
                       class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                        <i class="fas fa-plus mr-1"></i>
                        Ajouter un produit
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination compacte -->
    @if($products->hasPages())
        <div class="bg-white rounded-lg p-3 border border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-gray-700">
                    Page <span class="font-semibold">{{ $products->currentPage() }}</span> sur
                    <span class="font-semibold">{{ $products->lastPage() }}</span>
                </div>
                <div class="flex items-center gap-1">
                    {{ $products->links('vendor.pagination.simple-tailwind') }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- JavaScript -->
<script>
function confirmDelete(productName) {
    return confirm(`Supprimer "${productName}" ?\nCette action est irréversible.`);
}

document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const stickyHeader = document.getElementById('stickyHeader');
    const searchForm = document.getElementById('adminProductSearchForm');
    const searchInput = document.getElementById('searchInput');
    const filterToggle = document.getElementById('filterToggle');
    const filterDropdown = document.getElementById('filterDropdown');
    const statusFilter = document.getElementById('statusFilter');
    const stockFilter = document.getElementById('stockFilter');
    const activeFiltersCount = document.getElementById('activeFiltersCount');
    let searchTimer;

    // 1. Gestion du scroll pour l'en-tête sticky
    window.addEventListener('scroll', function() {
        if (window.scrollY > 20) {
            stickyHeader.classList.add('shadow-sm', 'bg-white/95');
        } else {
            stickyHeader.classList.remove('shadow-sm', 'bg-white/95');
        }
    });

    // 2. Gestion du dropdown des filtres
    filterToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        filterDropdown.classList.toggle('hidden');
    });

    // Fermer le dropdown si on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!filterDropdown.contains(e.target) && !filterToggle.contains(e.target)) {
            filterDropdown.classList.add('hidden');
        }
    });

    // 3. Calculer les filtres actifs
    function updateActiveFilters() {
        let activeFilters = 0;
        if (searchInput.value.trim() !== '') activeFilters++;
        if (statusFilter.value !== '') activeFilters++;
        if (stockFilter.value !== '') activeFilters++;
        activeFiltersCount.textContent = activeFilters;
    }

    // 4. Interroger toute la base avant la pagination.
    function submitDatabaseSearch() {
        clearTimeout(searchTimer);
        filterDropdown.classList.add('hidden');
        searchForm.requestSubmit();
    }

    searchInput.addEventListener('input', function() {
        updateActiveFilters();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(submitDatabaseSearch, 500);
    });

    searchInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            clearTimeout(searchTimer);
        }
    });

    statusFilter.addEventListener('change', submitDatabaseSearch);
    stockFilter.addEventListener('change', submitDatabaseSearch);

    searchForm.addEventListener('submit', function() {
        clearTimeout(searchTimer);
    });

    // 5. Raccourci clavier Ctrl+F
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }

        // Fermer le dropdown avec Escape
        if (e.key === 'Escape' && !filterDropdown.classList.contains('hidden')) {
            filterDropdown.classList.add('hidden');
        }
    });

    // 6. Initialiser
    updateActiveFilters();

    if (searchInput.value !== '') {
        searchInput.focus();
        searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
    }
});
</script>

<style>
/* Styles pour l'en-tête sticky */
#stickyHeader {
    transition: all 0.2s ease;
}

/* Styles pour le dropdown */
#filterDropdown {
    animation: slideDown 0.2s ease-out;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Styles pour les écrans mobiles */
@media (max-width: 640px) {
    #stickyHeader {
        padding-left: 1rem;
        padding-right: 1rem;
        margin-left: -1rem;
        margin-right: -1rem;
    }

    /* Assurer que la barre de recherche prend toute la largeur */
    .w-full {
        width: 100%;
    }

    /* Ajustement du dropdown sur mobile */
    #filterDropdown {
        position: fixed;
        left: 50%;
        transform: translateX(-50%);
        top: 20%;
        width: 90%;
        max-width: 300px;
    }
}

/* Styles pour desktop */
@media (min-width: 641px) {
    /* La barre de recherche a une largeur flexible */
    .sm\\:w-auto {
        width: auto;
    }

    .sm\\:flex-1 {
        flex: 1;
    }
}

/* Amélioration de l'accessibilité */
button:focus-visible,
input:focus-visible,
select:focus-visible {
    outline: 2px solid #10B981;
    outline-offset: 2px;
}

/* Animation pour les lignes du tableau */
.product-row, .mobile-product-card {
    animation: fadeInRow 0.2s ease;
}

@keyframes fadeInRow {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Style pour le bouton de filtres quand actif */
#filterToggle.active {
    background-color: #10B981;
    color: white;
}

#filterToggle.active i {
    color: white;
}

/* Empêcher la sélection de texte dans les compteurs */
#productCount, #activeFiltersCount {
    user-select: none;
}
</style>
@endsection
