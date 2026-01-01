@extends('admin.layouts.app')

@section('title', 'Gestion des Réductions - Admin')
@section('header', 'Gestion des Réductions')
@section('subheader', 'Gérez toutes les promotions de votre boutique')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header avec bouton d'action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Toutes les Réductions</h2>
            <p class="text-gray-600 mt-1">{{ $discounts->total() }} promotion{{ $discounts->total() > 1 ? 's' : '' }} enregistrée{{ $discounts->total() > 1 ? 's' : '' }}</p>
        </div>
        
        <a href="{{ route('admin.discounts.create') }}" 
           class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-green-200 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center">
            <i class="fas fa-plus mr-2"></i> Nouvelle Réduction
        </a>
    </div>

    <!-- Tableau des réductions -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- En-tête du tableau -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Liste des réductions</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Trier par :</span>
                    <select class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option>Date de création</option>
                        <option>Pourcentage</option>
                        <option>Date de début</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Réduction</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($discounts as $discount)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <!-- Colonne Produit -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4">
                                    <!-- Image du produit -->
                                    <div class="flex-shrink-0">
                                        @if($discount->product && $discount->product->images->count() > 0)
                                            <div class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200">
                                                <img src="{{ asset('storage/' . $discount->product->images->first()->image_path) }}" 
                                                     alt="{{ $discount->product->name }}"
                                                     class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-gray-100 to-gray-200 border border-gray-200 flex items-center justify-center">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Informations du produit -->
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <p class="font-semibold text-gray-800 truncate max-w-xs">
                                                {{ $discount->product->name ?? 'Produit non trouvé' }}
                                            </p>
                                            @if($discount->product && $discount->product->is_featured)
                                                <span class="text-yellow-500" title="Produit en vedette">
                                                    <i class="fas fa-star text-xs"></i>
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            @if($discount->product)
                                                <span class="font-medium">{{ number_format($discount->product->price, 2) }} DH</span>
                                                @if($discount->product->sku)
                                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $discount->product->sku }}</span>
                                                @endif
                                                @if($discount->product->category)
                                                    <span class="text-gray-500">{{ $discount->product->category->name }}</span>
                                                @endif
                                            @endif
                                        </div>
                                        
                                        <!-- Stock info -->
                                        @if($discount->product && $discount->product->stock_quantity <= $discount->product->low_stock_alert)
                                            <div class="mt-1">
                                                <span class="text-xs px-2 py-1 rounded-full {{ $discount->product->stock_quantity == 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                    <i class="fas {{ $discount->product->stock_quantity == 0 ? 'fa-times-circle' : 'fa-exclamation-triangle' }} mr-1"></i>
                                                    {{ $discount->product->stock_quantity == 0 ? 'Rupture de stock' : 'Stock faible' }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Colonne Réduction -->
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <!-- Pourcentage -->
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-3">
                                            <div class="text-2xl font-bold text-green-600">-{{ number_format($discount->discount_percentage, 2) }}%</div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Pourcentage</p>
                                            <p class="text-xs text-gray-500">de réduction</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Montant fixe -->
                                    @if($discount->product && $discount->product->price > 0)
                                        @php
                                            $fixedAmount = $discount->product->price * ($discount->discount_percentage / 100);
                                            $newPrice = $discount->product->price - $fixedAmount;
                                        @endphp
                                        <div class="space-y-1 text-sm">
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-600">Prix original :</span>
                                                <span class="font-semibold">{{ number_format($discount->product->price, 2) }} DH</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-600">Montant réduit :</span>
                                                <span class="font-semibold text-green-600">{{ number_format($fixedAmount, 2) }} DH</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-600">Nouveau prix :</span>
                                                <span class="font-bold text-green-700">{{ number_format($newPrice, 2) }} DH</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Colonne Période -->
                            <td class="px-6 py-4">
                                <div class="space-y-3">
                                    <!-- Dates -->
                                    <div class="space-y-2">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Début</p>
                                            <div class="flex items-center space-x-2">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                                    <i class="fas fa-play text-blue-500 text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-800">{{ $discount->start_date->format('d/m/Y') }}</p>
                                                    <p class="text-xs text-gray-500">{{ $discount->start_date->format('H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Fin</p>
                                            <div class="flex items-center space-x-2">
                                                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                                                    <i class="fas fa-stop text-purple-500 text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-800">{{ $discount->end_date->format('d/m/Y') }}</p>
                                                    <p class="text-xs text-gray-500">{{ $discount->end_date->format('H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Durée -->
                                    <div class="pt-2 border-t border-gray-100">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-clock text-gray-400 text-sm"></i>
                                            <span class="text-sm text-gray-600">
                                                {{ $discount->start_date->diffInDays($discount->end_date) }} jour(s)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Colonne Statut -->
                            <td class="px-6 py-4">
                                @php
                                    $now = now();
                                    $statusClass = '';
                                    $statusText = '';
                                    
                                    if ($discount->start_date > $now) {
                                        $statusClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                        $statusText = 'Programmée';
                                        $statusIcon = 'fa-calendar';
                                    } elseif ($discount->end_date > $now) {
                                        $statusClass = 'bg-green-50 text-green-700 border-green-200';
                                        $statusText = 'Active';
                                        $statusIcon = 'fa-check-circle';
                                    } else {
                                        $statusClass = 'bg-gray-50 text-gray-700 border-gray-200';
                                        $statusText = 'Expirée';
                                        $statusIcon = 'fa-times-circle';
                                    }
                                @endphp
                                
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full {{ strpos($statusClass, 'green') !== false ? 'bg-green-500 animate-pulse' : (strpos($statusClass, 'blue') !== false ? 'bg-blue-500' : 'bg-gray-400') }}"></div>
                                    <div class="flex flex-col">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                                            <i class="fas {{ $statusIcon }} mr-1"></i>
                                            {{ $statusText }}
                                        </span>
                                        @if($statusText === 'Active')
                                            <span class="text-xs text-gray-500 mt-1">
                                        J-{{ round($now->diffInDays($discount->end_date)) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Colonne Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <!-- Bouton Modifier -->
                                    <a href="{{ route('admin.discounts.edit', $discount) }}" 
                                       class="px-4 py-2 bg-gradient-to-r from-blue-50 to-blue-100 text-blue-600 font-medium rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-200 flex items-center space-x-2"
                                       title="Modifier la réduction">
                                        <i class="fas fa-edit"></i>
                                        <span class="hidden lg:inline">Modifier</span>
                                    </a>
                                    
                                    <!-- Bouton Supprimer -->
                                    <form action="{{ route('admin.discounts.destroy', $discount) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réduction ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-4 py-2 bg-gradient-to-r from-red-50 to-red-100 text-red-600 font-medium rounded-lg hover:from-red-100 hover:to-red-200 transition-all duration-200 flex items-center space-x-2"
                                                title="Supprimer la réduction">
                                            <i class="fas fa-trash"></i>
                                            <span class="hidden lg:inline">Supprimer</span>
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Informations supplémentaires -->
                                <div class="mt-4 text-xs text-gray-500 space-y-1">
                                    <p class="flex items-center">
                                        <i class="fas fa-calendar-plus mr-2"></i>
                                        Créée le {{ $discount->created_at->format('d/m/Y') }}
                                    </p>
                                    @if($discount->updated_at->gt($discount->created_at))
                                        <p class="flex items-center">
                                            <i class="fas fa-sync-alt mr-2"></i>
                                            Modifiée le {{ $discount->updated_at->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- État vide -->
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="max-w-md mx-auto">
                                    <div class="bg-gradient-to-r from-gray-100 to-gray-200 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-percentage text-gray-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-700 mb-2">Aucune réduction trouvée</h3>
                                    <p class="text-gray-500 mb-6">Créez votre première promotion pour augmenter vos ventes</p>
                                    <a href="{{ route('admin.discounts.create') }}" 
                                       class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-green-200 transition-all duration-200 inline-flex items-center">
                                        <i class="fas fa-plus mr-2"></i> Créer une réduction
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($discounts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $discounts->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Styles supplémentaires -->
<style>
    /* Animation pour les lignes */
    .hover\:bg-gray-50 {
        transition: background-color 0.2s ease;
    }

    /* Animation pour le statut actif */
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    /* Style pour les boutons d'action */
    .bg-gradient-to-r {
        transition: all 0.3s ease;
    }

    /* Style pour les images de produit */
    .w-12.h-12 img {
        transition: transform 0.3s ease;
    }

    .w-12.h-12:hover img {
        transform: scale(1.1);
    }

    /* Style pour le placeholder d'image */
    .bg-gradient-to-r.from-gray-100.to-gray-200 {
        transition: transform 0.3s ease;
    }

    /* Style pour le tableau */
    table {
        border-collapse: separate;
        border-spacing: 0;
    }

    th {
        position: sticky;
        top: 0;
        background-color: #f9fafb;
        z-index: 10;
    }
</style>

<script>
// Confirmation avant suppression
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        form.onsubmit = function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer cette réduction ?')) {
                this.submit();
            }
        };
    });

    // Animation au survol des images
    document.querySelectorAll('.w-12.h-12 img').forEach(img => {
        img.parentElement.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        img.parentElement.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection