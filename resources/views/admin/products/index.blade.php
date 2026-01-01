@extends('admin.layouts.app')

@section('title', 'Gestion des Produits - Admin')
@section('header', 'Gestion des Produits')
@section('subheader', 'Liste et gestion de tous vos produits')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions et filtres -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Produits</h1>
            <p class="text-gray-600 mt-1">Gérez l'ensemble de votre catalogue produits</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
            <!-- Recherche -->
            <div class="relative w-full sm:w-64">
                <input type="text" 
                       placeholder="Rechercher un produit..." 
                       id="searchInput"
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <!-- Bouton d'ajout -->
            <a href="{{ route('admin.products.create') }}" 
               class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-medium rounded-lg hover:shadow-lg hover:shadow-green-200 transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>
                Nouveau produit
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total produits</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $products->total() }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-boxes text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">En rupture</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">
                        {{ \App\Models\Product::where('stock_quantity', 0)->count() }}
                    </p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Produits en vedette</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">
                        {{ \App\Models\Product::where('is_featured', true)->count() }}
                    </p>
                </div>
                <div class="bg-amber-100 p-3 rounded-lg">
                    <i class="fas fa-star text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Produits actifs</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">
                        {{ \App\Models\Product::where('is_active', true)->count() }}
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-toggle-on text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <select id="statusFilter" class="appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 cursor-pointer text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="active">Actifs</option>
                        <option value="inactive">Inactifs</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                </div>
                
                <div class="relative">
                    <select id="stockFilter" class="appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 cursor-pointer text-sm">
                        <option value="">Stock</option>
                        <option value="low">Faible stock</option>
                        <option value="out">Rupture</option>
                        <option value="sufficient">Stock suffisant</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
                </div>
                
                <button id="resetFilters" class="text-gray-600 hover:text-gray-900 text-sm flex items-center">
                    <i class="fas fa-redo mr-2"></i>
                    Réinitialiser
                </button>
            </div>
            
            <div class="flex items-center text-sm text-gray-500">
                <i class="fas fa-info-circle mr-2"></i>
                {{ $products->total() }} produit(s) au total
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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prix</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <!-- Colonne Produit -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 mr-4">
                                        @if($product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="h-12 w-12 rounded-lg object-cover border border-gray-200">
                                        @else
                                            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-500">SKU: {{ $product->sku ?: 'Non défini' }}</span>
                                            @if($product->hasDiscount())
                                                <span class="text-xs font-medium bg-red-100 text-red-800 px-2 py-0.5 rounded-full">
                                                    -{{ $product->activeDiscount->discount_percentage }}%
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Colonne Catégorie -->
                            <td class="px-6 py-4">
                                @if($product->category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-tag mr-1"></i>
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">Non catégorisé</span>
                                @endif
                            </td>
                            
                            <!-- Colonne Prix -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if($product->hasDiscount())
                                        <div class="flex items-center">
                                            <span class="text-lg font-bold text-green-600">{{ number_format($product->final_price, 2) }} DH</span>
                                            <span class="text-sm text-gray-400 line-through ml-2">{{ number_format($product->price, 2) }} DH</span>
                                        </div>
                                    @else
                                        <span class="text-lg font-bold text-gray-900">{{ number_format($product->price, 2) }} DH</span>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Colonne Stock -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <span class="{{ $product->isLowStock() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    @if($product->stock_quantity == 0)
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Rupture
                                        </span>
                                    @elseif($product->isLowStock())
                                        <span class="px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Faible
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            OK
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Colonne Statut -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @if($product->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-circle text-[8px] mr-1.5"></i>
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-circle text-[8px] mr-1.5"></i>
                                            Inactif
                                        </span>
                                    @endif
                                    
                                    @if($product->is_featured)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <i class="fas fa-star mr-1"></i>
                                            Vedette
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Colonne Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.products.show', $product) }}" 
                                       class="text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100 transition"
                                       title="Voir les détails">
                                        <i class="fas fa-eye w-5 h-5"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="text-blue-500 hover:text-blue-700 p-1.5 rounded-full hover:bg-blue-50 transition"
                                       title="Modifier">
                                        <i class="fas fa-edit w-5 h-5"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.products.destroy', $product) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirmDelete('{{ addslashes($product->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-500 hover:text-red-700 p-1.5 rounded-full hover:bg-red-50 transition"
                                                title="Supprimer">
                                            <i class="fas fa-trash w-5 h-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
                                <p class="text-gray-500 mb-6">Commencez par ajouter votre premier produit</p>
                                <a href="{{ route('admin.products.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter un produit
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Vue mobile -->
        <div class="lg:hidden space-y-4 p-4">
            @forelse($products as $product)
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <!-- En-tête mobile -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-start space-x-3">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="h-16 w-16 rounded-lg object-cover border border-gray-200">
                            @else
                                <div class="h-16 w-16 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400 text-xl"></i>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">SKU: {{ $product->sku ?: 'Non défini' }}</p>
                                @if($product->category)
                                    <span class="inline-block mt-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                        {{ $product->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            @if($product->is_active)
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            @else
                                <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                            @endif
                        </div>
                    </div>

                    <!-- Informations principales -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Prix</p>
                            @if($product->hasDiscount())
                                <div>
                                    <span class="text-lg font-bold text-green-600">{{ number_format($product->final_price, 2) }} DH</span>
                                    <span class="text-sm text-gray-400 line-through ml-2">{{ number_format($product->price, 2) }} DH</span>
                                </div>
                                @if($product->activeDiscount)
                                    <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full mt-1 inline-block">
                                        -{{ $product->activeDiscount->discount_percentage }}%
                                    </span>
                                @endif
                            @else
                                <span class="text-lg font-bold text-gray-900">{{ number_format($product->price, 2) }} DH</span>
                            @endif
                        </div>
                        
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Stock</p>
                            <div class="flex items-center space-x-2">
                                <span class="{{ $product->isLowStock() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                    {{ $product->stock_quantity }}
                                </span>
                                @if($product->stock_quantity == 0)
                                    <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Rupture
                                    </span>
                                @elseif($product->isLowStock())
                                    <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Faible
                                    </span>
                                @else
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        OK
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Badges de statut -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if($product->is_active)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                <i class="fas fa-circle text-[6px] mr-1"></i>
                                Actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                <i class="fas fa-circle text-[6px] mr-1"></i>
                                Inactif
                            </span>
                        @endif
                        
                        @if($product->is_featured)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-amber-100 text-amber-800">
                                <i class="fas fa-star mr-1"></i>
                                Vedette
                            </span>
                        @endif
                    </div>

                    <!-- Actions mobile -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.products.show', $product) }}" 
                               class="text-gray-600 hover:text-gray-900 flex items-center">
                                <i class="fas fa-eye mr-2"></i>
                                Voir
                            </a>
                            
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="text-blue-600 hover:text-blue-800 flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Modifier
                            </a>
                        </div>
                        
                        <form action="{{ route('admin.products.destroy', $product) }}" 
                              method="POST"
                              onsubmit="return confirmDelete('{{ addslashes($product->name) }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-500 mb-6">Commencez par ajouter votre premier produit</p>
                    <a href="{{ route('admin.products.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>
                        Ajouter un produit
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                <div class="text-sm text-gray-700">
                    Affichage de <span class="font-semibold">{{ $products->firstItem() }}</span>
                    à <span class="font-semibold">{{ $products->lastItem() }}</span>
                    sur <span class="font-semibold">{{ $products->total() }}</span> produits
                </div>
                <div class="flex items-center space-x-2">
                    {{ $products->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- JavaScript pour les interactions -->
<script>
function confirmDelete(productName) {
    return confirm(`Êtes-vous sûr de vouloir supprimer "${productName}" ?\n\nCette action est irréversible.`);
}

// Filtrage et recherche
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const stockFilter = document.getElementById('stockFilter');
    const resetBtn = document.getElementById('resetFilters');
    const productRows = document.querySelectorAll('tbody tr');
    const mobileCards = document.querySelectorAll('.lg\\:hidden .bg-gray-50');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const stockValue = stockFilter.value;
        
        // Pour desktop
        productRows.forEach(row => {
            if (row.cells.length < 6) return; // Skip empty row
            
            const productName = row.cells[0].querySelector('.font-medium')?.textContent.toLowerCase() || '';
            const sku = row.cells[0].querySelector('.text-xs')?.textContent.toLowerCase() || '';
            const isActive = row.cells[4].querySelector('.bg-green-100') ? 'active' : 'inactive';
            const stockElement = row.cells[3].querySelector('span:first-child');
            const stockText = stockElement?.textContent.trim() || '0';
            const stock = parseInt(stockText);
            
            // Recherche
            const matchesSearch = searchTerm === '' || 
                productName.includes(searchTerm) || 
                sku.includes(searchTerm);
            
            // Filtre statut
            const matchesStatus = statusValue === '' || 
                (statusValue === 'active' && isActive === 'active') ||
                (statusValue === 'inactive' && isActive === 'inactive');
            
            // Filtre stock
            let matchesStock = true;
            if (stockValue === 'low' && !(stock > 0 && stock <= 10)) matchesStock = false;
            if (stockValue === 'out' && stock !== 0) matchesStock = false;
            if (stockValue === 'sufficient' && !(stock > 10)) matchesStock = false;
            
            // Afficher/masquer
            row.style.display = (matchesSearch && matchesStatus && matchesStock) ? '' : 'none';
        });
        
        // Pour mobile
        mobileCards.forEach(card => {
            const productName = card.querySelector('h3')?.textContent.toLowerCase() || '';
            const skuElement = card.querySelector('.text-sm.text-gray-500');
            const sku = skuElement?.textContent.toLowerCase() || '';
            const isActive = card.querySelector('.bg-green-100') ? 'active' : 'inactive';
            const stockElement = card.querySelector('.text-gray-700, .text-red-600');
            const stockText = stockElement?.textContent.trim().split(' ')[0] || '0';
            const stock = parseInt(stockText);
            
            // Recherche
            const matchesSearch = searchTerm === '' || 
                productName.includes(searchTerm) || 
                sku.includes(searchTerm);
            
            // Filtre statut
            const matchesStatus = statusValue === '' || 
                (statusValue === 'active' && isActive === 'active') ||
                (statusValue === 'inactive' && isActive === 'inactive');
            
            // Filtre stock
            let matchesStock = true;
            if (stockValue === 'low' && !(stock > 0 && stock <= 10)) matchesStock = false;
            if (stockValue === 'out' && stock !== 0) matchesStock = false;
            if (stockValue === 'sufficient' && !(stock > 10)) matchesStock = false;
            
            // Afficher/masquer
            card.style.display = (matchesSearch && matchesStatus && matchesStock) ? '' : 'none';
        });
    }
    
    // Événements
    searchInput.addEventListener('input', filterProducts);
    statusFilter.addEventListener('change', filterProducts);
    stockFilter.addEventListener('change', filterProducts);
    
    // Réinitialisation
    resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        stockFilter.value = '';
        filterProducts();
    });
    
    // Initialiser le filtrage
    filterProducts();
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
.fa-eye, .fa-edit, .fa-trash {
    transition: transform 0.2s ease;
}

.fa-eye:hover, .fa-edit:hover, .fa-trash:hover {
    transform: scale(1.1);
}

/* Style pour les badges */
[class*="bg-"]:not(.bg-white):not(.bg-gray-50):not(.bg-gray-100) {
    transition: opacity 0.2s ease;
}

[class*="bg-"]:not(.bg-white):not(.bg-gray-50):not(.bg-gray-100):hover {
    opacity: 0.9;
}
</style>
@endsection