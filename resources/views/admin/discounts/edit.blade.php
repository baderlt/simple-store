@extends('admin.layouts.app')

@section('title', 'Modifier une Réduction - Admin')
@section('header', 'Modifier la Réduction')
@section('subheader', 'Mettre à jour les détails de la promotion')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Card Container -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-3 rounded-xl shadow">
                        <i class="fas fa-percentage text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Modifier la Réduction</h2>
                        <p class="text-gray-600">Mettre à jour les informations de la promotion</p>
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
        <form action="{{ route('admin.discounts.update', $discount) }}" method="POST" class="p-8" id="discountForm">
            @csrf
            @method('PUT')
            
            <!-- Informations de base -->
            <div class="mb-10">
                <div class="flex items-center mb-6">
                    <div class="w-1 h-8 bg-blue-500 rounded-full mr-3"></div>
                    <h3 class="text-lg font-bold text-gray-800">Informations de base</h3>
                </div>
                
                <!-- Produit associé (affichage seulement) -->
                <div class="mb-6 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white p-3 rounded-xl shadow">
                                <i class="fas fa-box text-blue-500 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">Produit associé</h4>
                                <p class="text-sm text-gray-600">Cette réduction est appliquée à ce produit</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-800">{{ $discount->product->name ?? 'Non spécifié' }}</div>
                            @if($discount->product->sku)
                                <div class="text-sm text-gray-500">SKU: {{ $discount->product->sku }}</div>
                            @endif
                        </div>
                    </div>
                    
                    @if($discount->product)
                        <div class="mt-4 grid md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <p class="text-xs text-gray-500">Catégorie</p>
                                <p class="font-semibold text-gray-800">{{ $discount->product->category->name ?? 'Non catégorisé' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs text-gray-500">Prix actuel</p>
                                <p class="font-semibold text-gray-800">{{ number_format($discount->product->price, 2) }} DH</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="space-y-6">
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
                                       value="{{ old('discount_percentage', $discount->discount_percentage) }}"
                                       id="discountPercentage"
                                       placeholder="ex: 15.50"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('discount_percentage') border-red-500 ring-2 ring-red-200 @enderror">
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
                                       value="{{ old('fixed_amount', $discount->fixed_amount) }}"
                                       id="fixedAmount"
                                       placeholder="ex: 25.00"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('fixed_amount') border-red-500 ring-2 ring-red-200 @enderror">
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

                    <!-- Informations du produit sélectionné -->
                    <div id="productInfo" class="mt-3 bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <p class="text-xs text-gray-500">Prix original</p>
                                <p class="font-semibold text-gray-800" id="originalPrice">{{ number_format($discount->product->price ?? 0, 2) }} DH</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs text-gray-500">Nouveau prix</p>
                                <p class="font-bold text-green-600 text-lg" id="discountedPrice">
                                    @php
                                        $originalPrice = $discount->product->price ?? 0;
                                        $discountedPrice = $originalPrice;
                                        if($discount->discount_percentage) {
                                            $discountedPrice = $originalPrice - ($originalPrice * ($discount->discount_percentage / 100));
                                        } elseif($discount->fixed_amount) {
                                            $discountedPrice = $originalPrice - $discount->fixed_amount;
                                        }
                                    @endphp
                                    {{ number_format(max(0, $discountedPrice), 2) }} DH
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs text-gray-500">Économie</p>
                                <p class="font-bold text-purple-600" id="savingsAmount">
                                    @php
                                        $savings = 0;
                                        if($discount->discount_percentage) {
                                            $savings = $originalPrice * ($discount->discount_percentage / 100);
                                        } elseif($discount->fixed_amount) {
                                            $savings = $discount->fixed_amount;
                                        }
                                    @endphp
                                    {{ number_format(max(0, $savings), 2) }} DH d'économie
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Période de validité -->
            <div class="mb-10">
                <div class="flex items-center mb-6">
                    <div class="w-1 h-8 bg-purple-500 rounded-full mr-3"></div>
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
                                   value="{{ old('start_date', $discount->start_date->format('Y-m-d\TH:i')) }}"
                                   id="startDate"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('start_date') border-red-500 ring-2 ring-red-200 @enderror">
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
                                   value="{{ old('end_date', $discount->end_date->format('Y-m-d\TH:i')) }}"
                                   id="endDate"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('end_date') border-red-500 ring-2 ring-red-200 @enderror">
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
                <div id="durationIndicator" class="mt-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-purple-100 p-2 rounded-lg">
                                <i class="fas fa-clock text-purple-500"></i>
                            </div>
                            <div>
                                @php
                                    $start = $discount->start_date;
                                    $end = $discount->end_date;
                                    $now = now();
                                    $diffDays = $start->diffInDays($end);
                                    
                                    if ($start > $now) {
                                        $statusText = 'Programmée';
                                        $statusClass = 'text-blue-600';
                                    } elseif ($end > $now) {
                                        $statusText = 'Active';
                                        $statusClass = 'text-green-600';
                                    } else {
                                        $statusText = 'Expirée';
                                        $statusClass = 'text-red-600';
                                    }
                                @endphp
                                <p class="font-semibold text-gray-800" id="durationText">Durée : {{ $diffDays }} jour(s)</p>
                                <p class="text-sm text-gray-500" id="dateRange">
                                    {{ $start->format('d/m/Y H:i') }} → {{ $end->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Statut</p>
                            <p id="discountStatus" class="font-bold {{ $statusClass }}">{{ $statusText }}</p>
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
                                       {{ old('is_active', $discount->is_active) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-all duration-200"></div>
                                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-200 peer-checked:transform peer-checked:translate-x-6"></div>
                            </label>
                            <div>
                                <label for="is_active" class="font-semibold text-gray-700 cursor-pointer">Réduction Active</label>
                                <p class="text-sm text-gray-500 mt-1">La promotion sera visible sur le site</p>
                            </div>
                        </div>
                        <div id="is_active_icon" class="{{ old('is_active', $discount->is_active) ? 'text-green-500' : 'text-gray-400' }}">
                            <i class="fas fa-toggle-{{ old('is_active', $discount->is_active) ? 'on' : 'off' }} text-2xl"></i>
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
                                <h4 class="font-bold text-gray-800">Aperçu de la réduction</h4>
                                <p class="text-sm text-gray-600">Les détails ci-dessous seront visibles par les clients</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-green-600" id="summaryDiscount">
                                @if($discount->discount_percentage)
                                    {{ number_format($discount->discount_percentage, 2) }}%
                                @elseif($discount->fixed_amount)
                                    Montant fixe
                                @else
                                    0%
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">de réduction</div>
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Produit :</span>
                                <span class="font-semibold text-gray-800">{{ $discount->product->name ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Prix original :</span>
                                <span class="font-semibold text-gray-800" id="summaryOriginalPrice">{{ number_format($discount->product->price ?? 0, 2) }} DH</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Nouveau prix :</span>
                                <span class="font-bold text-green-600 text-lg" id="summaryNewPrice">
                                    @php
                                        $originalPrice = $discount->product->price ?? 0;
                                        $newPrice = $originalPrice;
                                        if($discount->discount_percentage) {
                                            $newPrice = $originalPrice - ($originalPrice * ($discount->discount_percentage / 100));
                                        } elseif($discount->fixed_amount) {
                                            $newPrice = $originalPrice - $discount->fixed_amount;
                                        }
                                    @endphp
                                    {{ number_format(max(0, $newPrice), 2) }} DH
                                </span>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Économie :</span>
                                <span class="font-bold text-purple-600" id="summarySavings">
                                    @php
                                        $savings = 0;
                                        if($discount->discount_percentage) {
                                            $savings = $originalPrice * ($discount->discount_percentage / 100);
                                        } elseif($discount->fixed_amount) {
                                            $savings = $discount->fixed_amount;
                                        }
                                    @endphp
                                    {{ number_format(max(0, $savings), 2) }} DH
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Validité :</span>
                                <span class="font-semibold text-gray-800" id="summaryValidity">
                                    {{ $discount->start_date->format('d/m/Y H:i') }} → {{ $discount->end_date->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Statut :</span>
                                <span class="font-bold {{ $statusClass }}" id="summaryStatus">{{ $statusText }}</span>
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
                        <span class="text-sm">Créé le {{ $discount->created_at->format('d/m/Y') }}</span>
                        <span class="mx-2">•</span>
                        <span class="text-sm">Modifié le {{ $discount->updated_at->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.discounts.index') }}" 
                           class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </a>
                        
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-200 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Mettre à jour la réduction
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript pour les fonctionnalités -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les événements
    initEventListeners();
    
    // Initialiser les icônes de checkbox
    updateCheckboxIcon('is_active', document.getElementById('is_active').checked);
    
    // Calculer les prix initiaux
    calculateDiscount();
    calculateDuration();
});

// Initialiser les événements
function initEventListeners() {
    // Pourcentage de réduction
    document.getElementById('discountPercentage').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('fixedAmount').value = '';
        }
        calculateDiscount();
        updateSummary();
    });
    
    // Montant fixe
    document.getElementById('fixedAmount').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('discountPercentage').value = '';
        }
        calculateDiscount();
        updateSummary();
    });
    
    // Dates
    document.getElementById('startDate').addEventListener('change', function() {
        calculateDuration();
        updateSummary();
    });
    
    document.getElementById('endDate').addEventListener('change', function() {
        calculateDuration();
        updateSummary();
    });
    
    // Checkbox active
    document.getElementById('is_active').addEventListener('change', function() {
        updateCheckboxIcon('is_active', this.checked);
        updateSummary();
    });
}

// Calculer le prix après réduction
function calculateDiscount() {
    const discountPercentage = document.getElementById('discountPercentage');
    const fixedAmount = document.getElementById('fixedAmount');
    const productInfo = document.getElementById('productInfo');
    
    // Récupérer le prix original depuis le DOM
    const originalPriceText = document.getElementById('originalPrice').textContent;
    const originalPrice = parseFloat(originalPriceText.replace(' DH', '').replace(',', '').trim()) || 0;
    
    if (originalPrice > 0 && (discountPercentage.value || fixedAmount.value)) {
        let discountedPrice = originalPrice;
        let savings = 0;
        
        if (discountPercentage.value) {
            const discount = parseFloat(discountPercentage.value) / 100;
            savings = originalPrice * discount;
            discountedPrice = originalPrice - savings;
        } else if (fixedAmount.value) {
            savings = parseFloat(fixedAmount.value);
            discountedPrice = originalPrice - savings;
        }
        
        // S'assurer que le prix n'est pas négatif
        if (discountedPrice < 0) discountedPrice = 0;
        if (savings < 0) savings = 0;
        
        // Afficher les informations
        document.getElementById('originalPrice').textContent = originalPrice.toFixed(2) + ' DH';
        document.getElementById('discountedPrice').textContent = discountedPrice.toFixed(2) + ' DH';
        document.getElementById('savingsAmount').textContent = savings.toFixed(2) + ' DH d\'économie';
        productInfo.classList.remove('hidden');
    } else {
        // Afficher les valeurs par défaut
        document.getElementById('discountedPrice').textContent = originalPrice.toFixed(2) + ' DH';
        document.getElementById('savingsAmount').textContent = '0.00 DH d\'économie';
    }
}

// Calculer la durée de validité
function calculateDuration() {
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const durationIndicator = document.getElementById('durationIndicator');
    
    if (startDate.value && endDate.value) {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const now = new Date();
        
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        let statusText = '';
        let statusClass = '';
        
        if (start > now) {
            statusText = 'Programmée';
            statusClass = 'text-blue-600';
        } else if (end > now) {
            statusText = 'Active';
            statusClass = 'text-green-600';
        } else {
            statusText = 'Expirée';
            statusClass = 'text-red-600';
        }
        
        document.getElementById('durationText').textContent = `Durée : ${diffDays} jour(s)`;
        document.getElementById('dateRange').textContent = 
            `${start.toLocaleDateString('fr-FR')} ${start.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })} → ${end.toLocaleDateString('fr-FR')} ${end.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}`;
        document.getElementById('discountStatus').textContent = statusText;
        document.getElementById('discountStatus').className = `font-bold ${statusClass}`;
        
        durationIndicator.classList.remove('hidden');
    }
}

// Mettre à jour le résumé
function updateSummary() {
    const discountPercentage = document.getElementById('discountPercentage');
    const fixedAmount = document.getElementById('fixedAmount');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const isActive = document.getElementById('is_active');
    
    // Récupérer le prix original depuis le DOM
    const originalPriceText = document.getElementById('summaryOriginalPrice').textContent;
    const originalPrice = parseFloat(originalPriceText.replace(' DH', '').replace(',', '').trim()) || 0;
    
    if (originalPrice > 0) {
        let discountValue = 0;
        let newPrice = originalPrice;
        let savings = 0;
        
        if (discountPercentage.value) {
            discountValue = parseFloat(discountPercentage.value);
            savings = originalPrice * (discountValue / 100);
            newPrice = originalPrice - savings;
        } else if (fixedAmount.value) {
            savings = parseFloat(fixedAmount.value);
            discountValue = (savings / originalPrice) * 100;
            newPrice = originalPrice - savings;
        }
        
        // S'assurer que les valeurs ne sont pas négatives
        if (newPrice < 0) newPrice = 0;
        if (savings < 0) savings = 0;
        if (discountValue < 0) discountValue = 0;
        
        // Mettre à jour les valeurs du résumé
        document.getElementById('summaryDiscount').textContent = 
            discountPercentage.value ? `${discountValue.toFixed(2)}%` : 'Montant fixe';
        document.getElementById('summaryNewPrice').textContent = newPrice.toFixed(2) + ' DH';
        document.getElementById('summarySavings').textContent = savings.toFixed(2) + ' DH';
        
        // Validité
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            document.getElementById('summaryValidity').textContent = 
                `${start.toLocaleDateString('fr-FR')} ${start.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })} → ${end.toLocaleDateString('fr-FR')} ${end.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}`;
        }
        
        // Statut
        document.getElementById('summaryStatus').textContent = isActive.checked ? 'Active' : 'Inactive';
        document.getElementById('summaryStatus').className = 
            isActive.checked ? 'font-bold text-green-600' : 'font-bold text-gray-600';
    }
}

// Mettre à jour l'icône de checkbox
function updateCheckboxIcon(checkboxId, isChecked) {
    const icon = document.getElementById(`${checkboxId}_icon`);
    if (!icon) return;
    
    if (isChecked) {
        icon.innerHTML = '<i class="fas fa-toggle-on text-2xl text-green-500"></i>';
        icon.classList.remove('text-gray-400');
        icon.classList.add('text-green-500');
    } else {
        icon.innerHTML = '<i class="fas fa-toggle-off text-2xl text-gray-400"></i>';
        icon.classList.remove('text-green-500');
        icon.classList.add('text-gray-400');
    }
}

// Validation du formulaire
document.getElementById('discountForm')?.addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('border-red-500', 'ring-2', 'ring-red-200');
            
            if (!field.nextElementSibling?.classList.contains('text-red-600')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'flex items-center text-red-600 text-sm mt-1';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Ce champ est obligatoire';
                field.parentNode.insertBefore(errorDiv, field.nextSibling);
            }
        }
    });
    
    // Vérifier que la date de fin est après la date de début
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    
    if (startDate.value && endDate.value) {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        
        if (end <= start) {
            isValid = false;
            alert('La date de fin doit être après la date de début');
        }
    }
    
    // Vérifier qu'un type de réduction est défini
    const discountPercentage = document.getElementById('discountPercentage');
    const fixedAmount = document.getElementById('fixedAmount');
    if (!discountPercentage.value && !fixedAmount.value) {
        isValid = false;
        discountPercentage.classList.add('border-red-500', 'ring-2', 'ring-red-200');
        alert('Veuillez entrer un pourcentage ou un montant fixe de réduction');
    }
    
    if (!isValid) {
        e.preventDefault();
        const firstError = this.querySelector('.border-red-500');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    }
});

// Supprimer les classes d'erreur lors de la saisie
document.querySelectorAll('input, select, textarea').forEach(field => {
    field.addEventListener('input', function() {
        this.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
        
        const errorMsg = this.nextElementSibling;
        if (errorMsg?.classList.contains('text-red-600')) {
            errorMsg.remove();
        }
    });
});
</script>

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

/* Animation pour les cartes */
.bg-gray-50 {
    transition: all 0.3s ease;
}

.bg-gray-50:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}
</style>
@endsection