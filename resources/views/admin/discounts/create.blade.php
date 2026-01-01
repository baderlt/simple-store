@extends('admin.layouts.app')

@section('title', 'Ajouter une Réduction - Admin')
@section('header', 'Ajouter une Réduction')
@section('subheader', 'Créer une nouvelle promotion pour vos produits')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Card Container -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-3 rounded-xl shadow">
                        <i class="fas fa-percentage text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Nouvelle Réduction</h2>
                        <p class="text-gray-600">Configurez une promotion spéciale pour vos produits</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-500">Les champs marqués d'un</span>
                    <span class="text-red-500 font-bold">*</span>
                    <span class="text-sm font-medium text-gray-500">sont obligatoires</span>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('admin.discounts.store') }}" method="POST" class="p-8" id="discountForm">
            @csrf
            
            <!-- Informations de base -->
            <div class="mb-10">
                <div class="flex items-center mb-6">
                    <div class="w-1 h-8 bg-purple-500 rounded-full mr-3"></div>
                    <h3 class="text-lg font-bold text-gray-800">Informations de base</h3>
                </div>
                
                <div class="space-y-6">
                    <!-- Type de réduction (Simplifié - uniquement produit) -->
                    <div class="hidden">
                        <input type="hidden" name="type" value="product">
                    </div>

                    <!-- Sélection du produit avec recherche -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center">
                            Produit *
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        
                        <!-- Champ de recherche -->
                        <div class="relative mb-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   id="productSearch"
                                   placeholder="Rechercher un produit par nom, SKU ou catégorie..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200">
                        </div>

                        <!-- Liste des produits filtrés -->
                        <div id="productList" class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg hidden">
                            <!-- Les produits filtrés apparaîtront ici -->
                        </div>

                        <!-- Sélecteur de produit (caché - utilisé pour le formulaire) -->
                        <select name="product_id" 
                                required
                                id="productSelect"
                                class="hidden">
                            <option value="" disabled selected>Sélectionnez un produit...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->stock_quantity }}"
                                        data-sku="{{ $product->sku }}"
                                        data-name="{{ strtolower($product->name) }}"
                                        data-category="{{ strtolower($product->category->name ?? '') }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} 
                                    @if($product->sku)
                                        ({{ $product->sku }})
                                    @endif
                                    - {{ number_format($product->price, 2) }} DH
                                    @if($product->category)
                                        - {{ $product->category->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        
                        <!-- Produit sélectionné affiché -->
                        <div id="selectedProduct" class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-800" id="selectedProductName">-</h4>
                                    <div class="flex items-center space-x-4 mt-1">
                                        <span class="text-sm text-gray-600" id="selectedProductPrice">-</span>
                                        <span class="text-sm text-gray-600" id="selectedProductSKU">-</span>
                                        <span class="text-sm text-gray-600" id="selectedProductCategory">-</span>
                                    </div>
                                </div>
                                <button type="button" 
                                        onclick="clearProductSelection()"
                                        class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Informations du produit sélectionné -->
                        <div id="productInfo" class="mt-3 bg-gray-50 rounded-xl p-4 border border-gray-200 hidden">
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-500">Prix original</p>
                                    <p class="font-semibold text-gray-800" id="originalPrice">-</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-500">Nouveau prix</p>
                                    <p class="font-bold text-green-600 text-lg" id="discountedPrice">-</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-500">Économie</p>
                                    <p class="font-bold text-purple-600" id="savingsAmount">-</p>
                                </div>
                            </div>
                        </div>
                        
                        @error('product_id')
                            <div class="flex items-center text-red-600 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Pourcentage de réduction -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 flex items-center">
                                Pourcentage de réduction *
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-percent text-gray-400"></i>
                                </div>
                                <input type="number" 
                                       name="discount_percentage" 
                                       min="0" 
                                       max="100" 
                                       step="0.01" 
                                       value="{{ old('discount_percentage') }}"
                                       id="discountPercentage"
                                       placeholder="ex: 15.50"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('discount_percentage') border-red-500 ring-2 ring-red-200 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">%</span>
                                </div>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-2"></i>
                                Entrez un pourcentage entre 0 et 100
                            </div>
                            @error('discount_percentage')
                                <div class="flex items-center text-red-600 text-sm mt-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Montant fixe optionnel -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Montant fixe
                                <span class="text-xs font-normal text-gray-500 block mt-1">Alternative au pourcentage</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-money-bill-wave text-gray-400"></i>
                                </div>
                                <input type="number" 
                                       name="fixed_amount" 
                                       min="0" 
                                       step="0.01"
                                       value="{{ old('fixed_amount') }}"
                                       id="fixedAmount"
                                       placeholder="ex: 25.00"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('fixed_amount') border-red-500 ring-2 ring-red-200 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">DH</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-lightbulb mr-1"></i>
                                Utilisez soit le pourcentage, soit le montant fixe
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Période de validité -->
            <div class="mb-10">
                <div class="flex items-center mb-6">
                    <div class="w-1 h-8 bg-blue-500 rounded-full mr-3"></div>
                    <h3 class="text-lg font-bold text-gray-800">Période de validité</h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Date de début -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center">
                            Date et heure de début *
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-plus text-gray-400"></i>
                            </div>
                            <input type="datetime-local" 
                                   name="start_date" 
                                   required
                                   value="{{ old('start_date') }}"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('start_date') border-red-500 ring-2 ring-red-200 @enderror">
                        </div>
                        @error('start_date')
                            <div class="flex items-center text-red-600 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Date de fin -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center">
                            Date et heure de fin *
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-minus text-gray-400"></i>
                            </div>
                            <input type="datetime-local" 
                                   name="end_date" 
                                   required
                                   value="{{ old('end_date') }}"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('end_date') border-red-500 ring-2 ring-red-200 @enderror">
                        </div>
                        @error('end_date')
                            <div class="flex items-center text-red-600 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <!-- Indicateur de durée -->
                <div id="durationIndicator" class="mt-6 bg-gray-50 rounded-xl p-4 border border-gray-200 hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <i class="fas fa-clock text-blue-500"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800" id="durationText">Durée : </p>
                                <p class="text-sm text-gray-500" id="dateRange">-</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Statut</p>
                            <p id="discountStatus" class="font-bold text-amber-600">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="mb-10">
                <div class="flex items-center mb-6">
                    <div class="w-1 h-8 bg-green-500 rounded-full mr-3"></div>
                    <h3 class="text-lg font-bold text-gray-800">Options</h3>
                </div>
                
                <!-- Réduction active -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-green-300 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       id="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-all duration-200"></div>
                                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:transform peer-checked:translate-x-6"></div>
                            </label>
                            <div>
                                <label for="is_active" class="font-semibold text-gray-700 cursor-pointer">Réduction Active</label>
                                <p class="text-sm text-gray-500 mt-1">La promotion sera visible sur le site</p>
                            </div>
                        </div>
                        <div id="is_active_icon" class="{{ old('is_active', true) ? 'text-green-500' : 'text-gray-400' }}">
                            <i class="fas fa-toggle-{{ old('is_active', true) ? 'on' : 'off' }} text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résumé de la réduction -->
            <div class="mb-10">
                <div class="flex items-center mb-6">
                    <div class="w-1 h-8 bg-amber-500 rounded-full mr-3"></div>
                    <h3 class="text-lg font-bold text-gray-800">Résumé de la réduction</h3>
                </div>
                
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white p-3 rounded-xl shadow">
                                <i class="fas fa-tag text-amber-500 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800" id="summaryTitle">Aperçu de la réduction</h4>
                                <p class="text-sm text-gray-600">Les détails ci-dessous seront visibles par les clients</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-green-600" id="summaryDiscount">0%</div>
                            <div class="text-sm text-gray-500">de réduction</div>
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Produit :</span>
                                <span class="font-semibold text-gray-800" id="summaryProduct">-</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Prix original :</span>
                                <span class="font-semibold text-gray-800" id="summaryOriginalPrice">- DH</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Nouveau prix :</span>
                                <span class="font-bold text-green-600 text-lg" id="summaryNewPrice">- DH</span>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Économie :</span>
                                <span class="font-bold text-purple-600" id="summarySavings">- DH</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Validité :</span>
                                <span class="font-semibold text-gray-800" id="summaryValidity">-</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Statut :</span>
                                <span class="font-bold text-amber-600" id="summaryStatus">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-8 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-history mr-2"></i>
                        <span class="text-sm">Tous les changements seront enregistrés</span>
                    </div>
                    
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.discounts.index') }}" 
                           class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </a>
                        
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-purple-200 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center">
                            <i class="fas fa-percentage mr-2"></i>
                            Créer la réduction
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Add this modal at the bottom of your create.blade.php file -->
@if(session('overlap_confirmation') && session('overlapping_discounts'))
<div id="overlapModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl max-w-md mx-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">Conflit de réductions détecté</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mb-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                    <p class="text-yellow-700">
                        Il existe {{ session('overlapping_discounts')['count'] }} réduction(s) chevauchante(s) pour ce produit pendant la période sélectionnée.
                    </p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Nouvelle période :</span>
                    <span class="font-semibold">{{ session('overlapping_discounts')['new_start_date'] }} → {{ session('overlapping_discounts')['new_end_date'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Réductions en conflit :</span>
                    <span class="font-semibold text-red-600">{{ session('overlapping_discounts')['count'] }} réduction(s)</span>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-4">
            <form action="{{ route('admin.discounts.storeWithOverride') }}" method="POST" id="overrideForm">
                @csrf
                <!-- Hidden fields with original form data -->
                <input type="hidden" name="product_id" value="{{ old('product_id') }}">
                <input type="hidden" name="discount_percentage" value="{{ old('discount_percentage') }}">
                <input type="hidden" name="fixed_amount" value="{{ old('fixed_amount') }}">
                <input type="hidden" name="start_date" value="{{ old('start_date') }}">
                <input type="hidden" name="end_date" value="{{ old('end_date') }}">
                <input type="hidden" name="is_active" value="{{ old('is_active', 1) }}">
                
                <div class="flex flex-col space-y-3">
                    <button type="submit" name="override_existing" value="1" 
                            class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-red-200 transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-trash mr-2"></i>
                        Supprimer les réductions existantes et créer la nouvelle
                    </button>
                    
                    <button type="button" onclick="closeModal()" 
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                        Annuler et modifier la période
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function closeModal() {
    document.getElementById('overlapModal').remove();
}

// Show modal on page load
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('overlapModal');
    if (modal) {
        modal.style.display = 'flex';
    }
});
</script>
@endif
<!-- JavaScript pour la recherche et affichage des produits -->
<script src="{{asset('js/add-discount.js')}}"></script>

<style>
/* Animation pour les boutons */
button, a {
    transition: all 0.2s ease;
}

/* Style pour le placeholder */
::-webkit-input-placeholder {
    color: #9CA3AF;
    font-style: italic;
}

/* Style pour la recherche */
#productSearch {
    transition: all 0.3s ease;
}

#productSearch:focus {
    box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
}

/* Style pour la liste des produits */
#productList {
    background: white;
    z-index: 10;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

#productList div {
    transition: background-color 0.2s ease;
}

#productList div:hover {
    background-color: #f9fafb;
}

/* Style pour le produit sélectionné */
#selectedProduct {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Animation pour les cartes */
.bg-gray-50 {
    transition: all 0.3s ease;
}

.bg-gray-50:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

/* Scrollbar personnalisée */
#productList::-webkit-scrollbar {
    width: 8px;
}

#productList::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#productList::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

#productList::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection