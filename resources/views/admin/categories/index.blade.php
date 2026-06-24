@extends('admin.layouts.app')

@section('title', 'Gestion des Catégories - Admin')
@section('header', 'Gestion des Catégories')
@section('subheader', 'Organisez vos produits par catégorie')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Catégories</h1>
            <p class="text-gray-600 mt-1">Organisez et gérez vos catégories de produits</p>
        </div>
        <form id="categoryFilterForm" method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
            <!-- Recherche -->
            <div class="relative w-full sm:w-64">
                <input type="text"
                       placeholder="{{ __('admin.search_database_placeholder') }}"
                       id="searchInput"
                       name="search"
                       value="{{ request('search') }}"
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-900">
                <i class="fas fa-search mr-2"></i>
                {{ __('admin.search') }}
            </button>
            <!-- Bouton d'ajout -->
            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-medium rounded-lg hover:shadow-lg hover:shadow-green-200 transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle catégorie
            </a>
        </form>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total catégories</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $categories->total() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-folder text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Catégories actives</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">
                        {{ \App\Models\Category::where('is_active', true)->count() }}
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-toggle-on text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Produits catégorisés</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">
                        {{ \App\Models\Product::whereHas('category')->count() }}
                    </p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-boxes text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres simples -->
    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <select id="statusFilter" name="status" form="categoryFilterForm" class="appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 cursor-pointer text-sm">
                        <option value="">{{ __('admin.all_statuses') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('admin.active_feminine_plural') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('admin.inactive_feminine_plural') }}</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                </div>

                <a href="{{ route('admin.categories.index') }}" id="resetFilters" class="text-gray-600 hover:text-gray-900 text-sm flex items-center">
                    <i class="fas fa-redo mr-2"></i>
                    {{ __('admin.reset') }}
                </a>
            </div>

            <div class="flex items-center text-sm text-gray-500">
                <i class="fas fa-info-circle mr-2"></i>
                {{ $categories->total() }} catégorie(s) au total
            </div>
        </div>
    </div>

    <!-- Tableau des catégories -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Tableau desktop -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produits</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="categoriesTableBody">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 category-row"
                            data-name="{{ strtolower($category->name) }}"
                            data-slug="{{ strtolower($category->slug) }}"
                            data-status="{{ $category->is_active ? 'active' : 'inactive' }}">
                            <!-- Colonne Catégorie -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 mr-4">
                                        @if($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                 alt="{{ $category->name }}"
                                                 class="h-12 w-12 rounded-lg object-cover border border-gray-200">
                                        @else
                                            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                                <i class="fas fa-folder text-blue-400 text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">
                                            <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
                                               class="hover:text-blue-600 hover:underline">
                                                {{ $category->name }}
                                            </a>
                                        </p>
                                        @if($category->name_ar)
                                            <p class="mt-1 text-sm font-semibold text-emerald-700" dir="rtl">{{ $category->name_ar }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">
                                            @if($category->slug)
                                                <i class="fas fa-link mr-1"></i>
                                                {{ $category->slug }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Colonne Description -->
                            <td class="px-6 py-4">
                                @if($category->description)
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ $category->description }}</p>
                                @else
                                    <span class="text-gray-400 text-sm">Aucune description</span>
                                @endif
                            </td>

                            <!-- Colonne Produits -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($category->products_count > 0)
                                        <div class="relative group">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 flex items-center justify-center cursor-pointer"
                                                 title="{{ $category->products_count }} produits">
                                                <span class="font-bold text-blue-700">{{ $category->products_count }}</span>
                                            </div>
                                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs">
                                                <i class="fas fa-box"></i>
                                            </div>
                                            <!-- Tooltip des produits -->
                                            @if($category->products_count > 0)
                                                <div class="absolute left-0 top-full mt-2 w-64 bg-white shadow-lg rounded-lg border border-gray-200 p-3 z-10 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200">
                                                    <p class="text-sm font-medium text-gray-900 mb-2">{{ $category->products_count }} produit(s)</p>
                                                    <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
                                                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                                        <i class="fas fa-eye mr-2"></i>
                                                        Voir tous les produits
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            @if($category->products_count > 0)
                                                <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
                                                   class="text-sm text-blue-600 hover:text-blue-800 hover:underline flex items-center">
                                                    <i class="fas fa-external-link-alt mr-1 text-xs"></i>
                                                    Voir les produits
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex items-center text-gray-400">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-box-open"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm">Aucun produit</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Colonne Statut -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-2">
                                    @if($category->is_active)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">
                                            <i class="fas fa-circle text-[8px] mr-1.5"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 w-fit">
                                            <i class="fas fa-circle text-[8px] mr-1.5"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Colonne Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="text-blue-500 hover:text-blue-700 p-1.5 rounded-full hover:bg-blue-50 transition"
                                       title="Modifier">
                                        <i class="fas fa-edit w-5 h-5"></i>
                                    </a>

                                    <form action="{{ route('admin.categories.destroy', $category) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirmDelete('{{ addslashes($category->name) }}', {{ $category->products_count }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-500 hover:text-red-700 p-1.5 rounded-full hover:bg-red-50 transition"
                                                title="Supprimer">
                                            <i class="fas fa-trash w-5 h-5"></i>
                                        </button>
                                    </form>

                                    @if($category->products_count > 0)
                                        <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}"
                                           class="text-green-500 hover:text-green-700 p-1.5 rounded-full hover:bg-green-50 transition"
                                           title="Ajouter un produit">
                                            <i class="fas fa-plus w-5 h-5"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune catégorie trouvée</h3>
                                <p class="text-gray-500 mb-6">Commencez par créer votre première catégorie</p>
                                <a href="{{ route('admin.categories.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Créer une catégorie
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Vue mobile -->
        <div class="lg:hidden space-y-4 p-4" id="mobileCategoriesList">
            @forelse($categories as $category)
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 mobile-category-card"
                     data-name="{{ strtolower($category->name) }}"
                     data-slug="{{ strtolower($category->slug) }}"
                     data-status="{{ $category->is_active ? 'active' : 'inactive' }}">
                    <!-- En-tête mobile -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-start space-x-3">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}"
                                     alt="{{ $category->name }}"
                                     class="h-16 w-16 rounded-lg object-cover border border-gray-200">
                            @else
                                <div class="h-16 w-16 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                    <i class="fas fa-folder text-blue-400 text-xl"></i>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $category->name }}</h3>
                                @if($category->name_ar)
                                    <p class="mt-1 text-sm font-semibold text-emerald-700" dir="rtl">{{ $category->name_ar }}</p>
                                @endif
                                @if($category->slug)
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-link mr-1"></i>
                                        {{ $category->slug }}
                                    </p>
                                @endif
                                <!-- Statut mobile -->
                                <div class="flex items-center space-x-2 mt-2">
                                    @if($category->is_active)
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        <span class="text-xs text-green-600">Active</span>
                                    @else
                                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                        <span class="text-xs text-gray-600">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($category->description)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $category->description }}</p>
                        </div>
                    @endif

                    <!-- Informations principales -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Produits</p>
                            <div class="flex items-center">
                                @if($category->products_count > 0)
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 flex items-center justify-center">
                                        <span class="font-bold text-blue-700 text-sm">{{ $category->products_count }}</span>
                                    </div>
                                    @if($category->products_count > 0)
                                        <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
                                           class="text-xs text-blue-600 hover:text-blue-800 ml-2">
                                            Voir
                                        </a>
                                    @endif
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-box-open text-gray-400 text-sm"></i>
                                    </div>
                                    <span class="text-xs text-gray-500 ml-2">Aucun</span>
                                @endif
                            </div>
                        </div>

                        <!-- Dans la vue mobile, partie Promotions -->
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-1">Promotions</p>
                            <div class="flex items-center">
                                @php
                                    $activeDiscountsCount = $category->discounts_count ?? 0;
                                @endphp
                                @if($activeDiscountsCount > 0)
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-amber-100 to-orange-100 flex items-center justify-center">
                                        <span class="font-bold text-amber-700 text-sm">{{ $activeDiscountsCount }}</span>
                                    </div>
                                    <a href="{{ route('admin.discounts.index', ['category_id' => $category->id]) }}"
                                       class="text-xs text-amber-600 hover:text-amber-800 ml-2">
                                        Voir
                                    </a>
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-percent text-gray-400 text-sm"></i>
                                    </div>
                                    <span class="text-xs text-gray-500 ml-2">Aucune</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions mobile -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="text-blue-600 hover:text-blue-800 flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Modifier
                            </a>

                            @if($category->products_count > 0)
                                <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}"
                                   class="text-green-600 hover:text-green-800 flex items-center">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter produit
                                </a>
                            @endif
                        </div>

                        <form action="{{ route('admin.categories.destroy', $category) }}"
                              method="POST"
                              onsubmit="return confirmDelete('{{ addslashes($category->name) }}', {{ $category->products_count }})">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div id="mobileEmptyState" class="text-center py-12">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune catégorie trouvée</h3>
                    <p class="text-gray-500 mb-6">Commencez par créer votre première catégorie</p>
                    <a href="{{ route('admin.categories.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>
                        Créer une catégorie
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                <div class="text-sm text-gray-700">
                    Affichage de <span class="font-semibold">{{ $categories->firstItem() }}</span>
                    à <span class="font-semibold">{{ $categories->lastItem() }}</span>
                    sur <span class="font-semibold">{{ $categories->total() }}</span> catégories
                </div>
                <div class="flex items-center space-x-2">
                    {{ $categories->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- JavaScript pour les interactions -->
<script>
function confirmDelete(categoryName, productsCount) {
    let message = `Êtes-vous sûr de vouloir supprimer la catégorie "${categoryName}" ?`;

    if (productsCount > 0) {
        message += `\n\n⚠️ ATTENTION : Cette catégorie contient ${productsCount} produit(s).\n` +
                   `Tous les produits deviendront non classés.`;
    }

    message += `\n\nCette action est irréversible.`;

    return confirm(message);
}

// Filtrage et recherche SIMPLIFIÉ
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const resetBtn = document.getElementById('resetFilters');

    // Éléments à filtrer
    const desktopRows = document.querySelectorAll('.category-row');
    const mobileCards = document.querySelectorAll('.mobile-category-card');
    const emptyRow = document.getElementById('emptyRow');
    const mobileEmptyState = document.getElementById('mobileEmptyState');

    function filterCategories() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusValue = statusFilter.value;

        let hasVisibleDesktop = false;
        let hasVisibleMobile = false;

        // Filtrer le tableau desktop
        desktopRows.forEach(row => {
            const categoryName = row.getAttribute('data-name') || '';
            const categorySlug = row.getAttribute('data-slug') || '';
            const status = row.getAttribute('data-status');

            // Vérifier recherche
            const matchesSearch = searchTerm === '' ||
                categoryName.includes(searchTerm) ||
                categorySlug.includes(searchTerm);

            // Vérifier filtre statut
            const matchesStatus = statusValue === '' || status === statusValue;

            // Afficher/masquer
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                hasVisibleDesktop = true;
            } else {
                row.style.display = 'none';
            }
        });

        // Filtrer les cartes mobile
        mobileCards.forEach(card => {
            const categoryName = card.getAttribute('data-name') || '';
            const categorySlug = card.getAttribute('data-slug') || '';
            const status = card.getAttribute('data-status');

            // Vérifier recherche
            const matchesSearch = searchTerm === '' ||
                categoryName.includes(searchTerm) ||
                categorySlug.includes(searchTerm);

            // Vérifier filtre statut
            const matchesStatus = statusValue === '' || status === statusValue;

            // Afficher/masquer
            if (matchesSearch && matchesStatus) {
                card.style.display = '';
                hasVisibleMobile = true;
            } else {
                card.style.display = 'none';
            }
        });

        // Gérer l'état vide
        const allCategories = @json($categories->items());
        const hasCategories = allCategories.length > 0;

        if (emptyRow) {
            emptyRow.style.display = !hasCategories ? '' : 'none';
        }

        if (mobileEmptyState) {
            mobileEmptyState.style.display = !hasCategories ? '' : 'none';
        }

        // Si aucune catégorie ne correspond aux filtres mais qu'il y a des catégories
        if (hasCategories && !hasVisibleDesktop && !hasVisibleMobile) {
            // Montrer un message d'absence de résultats
            showNoResultsMessage(searchTerm, statusValue);
        } else {
            hideNoResultsMessage();
        }
    }

    function showNoResultsMessage(searchTerm, statusValue) {
        // Vérifier si le message existe déjà
        let messageElement = document.getElementById('noResultsMessage');

        if (!messageElement) {
            messageElement = document.createElement('tr');
            messageElement.id = 'noResultsMessage';
            messageElement.innerHTML = `
                <td colspan="5" class="px-6 py-12 text-center">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-search text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun résultat trouvé</h3>
                    <p class="text-gray-500 mb-6" id="filterMessage"></p>
                    <button onclick="resetFilters()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-redo mr-2"></i>
                        {{ __('admin.reset') }} les filtres
                    </button>
                </td>
            `;

            // Ajouter au tableau desktop
            const tbody = document.querySelector('#categoriesTableBody');
            if (tbody) {
                tbody.appendChild(messageElement);
            }

            // Ajouter aussi pour mobile
            const mobileContainer = document.getElementById('mobileCategoriesList');
            if (mobileContainer) {
                const mobileMessage = document.createElement('div');
                mobileMessage.id = 'mobileNoResultsMessage';
                mobileMessage.className = 'text-center py-12';
                mobileMessage.innerHTML = `
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-search text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun résultat trouvé</h3>
                    <p class="text-gray-500 mb-6" id="mobileFilterMessage"></p>
                    <button onclick="resetFilters()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-redo mr-2"></i>
                        {{ __('admin.reset') }} les filtres
                    </button>
                `;
                mobileContainer.appendChild(mobileMessage);
            }
        }

        // Mettre à jour le message
        let message = 'Aucune catégorie ne correspond à vos critères.';
        if (searchTerm && statusValue) {
            message = `Aucune catégorie "${searchTerm}" avec le statut "${getStatusLabel(statusValue)}" trouvée.`;
        } else if (searchTerm) {
            message = `Aucune catégorie contenant "${searchTerm}" n'a été trouvée.`;
        } else if (statusValue) {
            message = `Aucune catégorie avec le statut "${getStatusLabel(statusValue)}" trouvée.`;
        }

        const filterMessage = document.getElementById('filterMessage');
        const mobileFilterMessage = document.getElementById('mobileFilterMessage');

        if (filterMessage) filterMessage.textContent = message;
        if (mobileFilterMessage) mobileFilterMessage.textContent = message;
    }

    function hideNoResultsMessage() {
        const messageElement = document.getElementById('noResultsMessage');
        const mobileMessageElement = document.getElementById('mobileNoResultsMessage');

        if (messageElement) messageElement.remove();
        if (mobileMessageElement) mobileMessageElement.remove();
    }

    function getStatusLabel(statusValue) {
        switch(statusValue) {
            case 'active': return 'Active';
            case 'inactive': return 'Inactive';
            default: return statusValue;
        }
    }

    // Fonction pour réinitialiser les filtres
    window.resetFilters = function() {
        searchInput.value = '';
        statusFilter.value = '';
        filterCategories();
    };

    // Événements
    searchInput.addEventListener('input', filterCategories);
    statusFilter.addEventListener('change', filterCategories);

    // Événement pour le bouton reset
    if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
    }

    // Initialiser le filtrage
    filterCategories();
});

// Gestion des tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggers = document.querySelectorAll('.group.relative');

    tooltipTriggers.forEach(trigger => {
        const tooltip = trigger.querySelector('.absolute');
        if (tooltip) {
            trigger.addEventListener('mouseenter', () => {
                tooltip.classList.remove('invisible', 'opacity-0');
            });

            trigger.addEventListener('mouseleave', () => {
                tooltip.classList.add('invisible', 'opacity-0');
            });
        }
    });
});

// Gestion des prévisualisations d'image
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[src*="storage/"]');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.src = 'https://via.placeholder.com/100x100/e5e7eb/6b7280?text=Image+non+disponible';
            this.classList.add('object-contain', 'p-2');
        });
    });
});
</script>

<style>
/* Styles pour améliorer l'expérience mobile */
@media (max-width: 768px) {
    .hide-on-mobile {
        display: none !important;
    }
}

/* Animation pour les lignes du tableau */
tr {
    transition: background-color 0.2s ease;
}

/* Style pour les boutons d'action */
.fa-edit, .fa-trash, .fa-plus {
    transition: transform 0.2s ease;
}

.fa-edit:hover, .fa-trash:hover, .fa-plus:hover {
    transform: scale(1.1);
}

/* Style pour les badges */
[class*="bg-"]:not(.bg-white):not(.bg-gray-50):not(.bg-gray-100) {
    transition: opacity 0.2s ease;
}

[class*="bg-"]:not(.bg-white):not(.bg-gray-50):not(.bg-gray-100):hover {
    opacity: 0.9;
}

/* Limite de lignes pour la description */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Tooltip */
.group:hover .group-hover\:visible {
    visibility: visible;
}

.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}

/* Animation pour la recherche */
#searchInput:focus {
    transition: all 0.3s ease;
}
</style>
@endsection
