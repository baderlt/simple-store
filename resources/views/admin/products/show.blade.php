@extends('admin.layouts.app')

@section('title', $product->name . ' - Détails')
@section('header', 'Détails du Produit')
@section('subheader', 'Informations complètes et statistiques')

@section('content')
<style>
    .aspect-square {
    aspect-ratio: 1 / 1;
}

/* Animation pour les images */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Style pour les liens */
a:hover {
    transition: color 0.2s ease;
}

/* Style pour les tableaux */
table {
    border-collapse: separate;
    border-spacing: 0;
}

table th {
    font-weight: 600;
}

table tr {
    transition: background-color 0.2s ease;
}

/* Barre de progression */
.h-2 {
    height: 0.5rem;
}
</style>
<div class="max-w-7xl mx-auto">
    <!-- En-tête avec actions -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <i class="fas fa-home mr-2"></i>
                                Tableau de bord
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <a href="{{ route('admin.products.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600">
                                    Produits
                                </a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="ml-1 text-sm font-medium text-gray-500">{{ $product->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-gray-600 mt-2">Gestion et suivi des performances du produit</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.products.edit', $product) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <i class="fas fa-edit mr-2"></i> Modifier
                </a>
                <a href="{{ route('admin.products.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Informations produit -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Carte d'information principale -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            @if($product->images->where('is_primary', true)->first())
                                <img src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-20 h-20 rounded-lg object-cover border border-gray-200">
                            @else
                                <div class="w-20 h-20 bg-gradient-to-r from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                                <div class="flex items-center space-x-3 mt-2">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ $product->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                    @if($product->is_featured)
                                        <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm font-medium">
                                            <i class="fas fa-star mr-1"></i>
                                            En vedette
                                        </span>
                                    @endif
                                    @if($product->category)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                            <i class="fas fa-tag mr-1"></i>
                                            {{ $product->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-gray-900">{{ number_format($product->price, 2) }} DH</div>
                            <p class="text-sm text-gray-500 mt-1">Prix unitaire</p>
                        </div>
                    </div>

                    <!-- Informations détaillées -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Référence (SKU)</label>
                                <p class="mt-1 text-lg font-medium text-gray-900">{{ $product->sku ?: 'Non défini' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Description</label>
                                <p class="mt-1 text-gray-700">{{ $product->description ?: 'Aucune description' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Date de création</label>
                                <p class="mt-1 text-gray-700">{{ $product->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Dernière modification</label>
                                <p class="mt-1 text-gray-700">{{ $product->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Lien public</label>
                                <a href="{{ route('products.show', $product->slug) }}" 
                                   target="_blank"
                                   class="mt-1 inline-flex items-center text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Voir sur le site
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques de vente -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                        Performances commerciales
                    </h3>
                </div>
                <div class="p-6">
         <!-- Cartes de statistiques MODIFIÉES -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- Total des ventes VALIDES -->
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-blue-700">Ventes valides</p>
                <p class="text-2xl font-bold text-blue-900 mt-2">{{ $ordersData['total_orders'] }}</p>
                <p class="text-xs text-blue-600 mt-1">{{ $ordersData['total_quantity'] }} unités</p>
            </div>
            <div class="bg-blue-500 p-3 rounded-lg">
                <i class="fas fa-check-circle text-white"></i>
            </div>
        </div>
    </div>

    <!-- Chiffre d'affaires VALIDE -->
    <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-green-700">Chiffre d'affaires</p>
                <p class="text-2xl font-bold text-green-900 mt-2">{{ number_format($ordersData['total_revenue'], 2) }} DH</p>
                <p class="text-xs text-green-600 mt-1">
                    @if($ordersData['total_orders'] > 0)
                        Moyenne: {{ number_format($ordersData['average_order_value'], 2) }} DH
                    @else
                        Aucune vente
                    @endif
                </p>
            </div>
            <div class="bg-green-500 p-3 rounded-lg">
                <i class="fas fa-money-bill-wave text-white"></i>
            </div>
        </div>
    </div>

    <!-- Commandes annulées -->
    <div class="bg-gradient-to-r from-red-50 to-red-100 p-4 rounded-xl border border-red-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-red-700">Commandes annulées</p>
                <p class="text-2xl font-bold text-red-900 mt-2">{{ $ordersData['cancelled_orders_count'] }}</p>
                <p class="text-xs text-red-600 mt-1">
                    {{ $ordersData['cancelled_quantity'] }} unités
                    @if($ordersData['total_all_orders'] > 0)
                        ({{ $ordersData['cancellation_rate'] }}%)
                    @endif
                </p>
            </div>
            <div class="bg-red-500 p-3 rounded-lg">
                <i class="fas fa-times-circle text-white"></i>
            </div>
        </div>
    </div>

  
</div>
                </div>
            </div>

            <!-- Dernières commandes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-receipt mr-2 text-green-500"></i>
                            Dernières commandes
                        </h3>
                        @if($ordersData['total_orders'] > 0)
                            <span class="text-sm text-gray-500">{{ $ordersData['total_orders'] }} commande(s) au total</span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    @if($ordersData['recent_orders']->count() > 0)
                        <div class="overflow-hidden rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Commande</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($ordersData['recent_orders'] as $orderItem)
                                        @php
                                            $order = $orderItem->order;
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($order)
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" 
                                                       class="text-blue-600 hover:text-blue-900 font-medium inline-flex items-center">
                                                        {{ $order->order_number }}
                                                        <i class="fas fa-external-link-alt ml-2 text-xs"></i>
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $orderItem->created_at->format('d/m/Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $orderItem->created_at->format('H:i') }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                    {{ $orderItem->quantity }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($orderItem->price, 2) }} DH</div>
                                                @if($orderItem->discount_price && $orderItem->discount_price < $orderItem->price)
                                                    <div class="text-xs text-green-600">
                                                        <s>{{ number_format($orderItem->price, 2) }} DH</s>
                                                        → {{ number_format($orderItem->discount_price, 2) }} DH
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ number_format($orderItem->subtotal, 2) }} DH
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($order)
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $order->status_color }}">
                                                        {{ $order->status_label }}
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                        N/A
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($ordersData['total_orders'] > 10)
                            <div class="mt-4 text-center">
                                <a href="{{ route('admin.orders.index', ['product_id' => $product->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                    <i class="fas fa-list mr-2"></i>
                                    Voir toutes les commandes ({{ $ordersData['total_orders'] }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Aucune commande</h4>
                            <p class="text-gray-500">Ce produit n'a pas encore été commandé.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Colonne droite : Informations complémentaires -->
        <div class="space-y-6">
            <!-- Stock et inventaire -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-boxes mr-2 text-red-500"></i>
                        Stock & Inventaire
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <!-- Niveau de stock -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Niveau de stock</span>
                                <span class="text-lg font-bold text-gray-900">{{ $product->stock_quantity }}</span>
                            </div>
                            <!-- Barre de progression -->
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $stockPercentage = $product->stock_quantity > 0 
                                        ? min(100, ($product->stock_quantity / 100) * 100)
                                        : 0;
                                    
                                    if($product->stock_quantity == 0) {
                                        $stockColor = 'bg-red-500';
                                    } elseif($product->stock_quantity <= $product->low_stock_alert) {
                                        $stockColor = 'bg-amber-500';
                                    } else {
                                        $stockColor = 'bg-green-500';
                                    }
                                @endphp
                                <div class="h-2 rounded-full {{ $stockColor }}" style="width: {{ $stockPercentage }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">0</span>
                                <span class="text-xs text-gray-500">100+</span>
                            </div>
                        </div>

                        <!-- Alerte stock faible -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alerte stock faible</label>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-exclamation-triangle text-amber-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->low_stock_alert }} unités</p>
                                    <p class="text-xs text-gray-500">Alerte déclenchée en dessous de ce niveau</p>
                                </div>
                            </div>
                        </div>

                        <!-- État du stock -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">État actuel</label>
                            @if($product->stock_quantity == 0)
                                <div class="flex items-center p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <i class="fas fa-times-circle text-red-500 mr-3"></i>
                                    <div>
                                        <p class="font-medium text-red-700">Rupture de stock</p>
                                        <p class="text-sm text-red-600">Le produit est épuisé</p>
                                    </div>
                                </div>
                            @elseif($product->stock_quantity <= $product->low_stock_alert)
                                <div class="flex items-center p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                    <i class="fas fa-exclamation-triangle text-amber-500 mr-3"></i>
                                    <div>
                                        <p class="font-medium text-amber-700">Stock faible</p>
                                        <p class="text-sm text-amber-600">Seulement {{ $product->stock_quantity }} unités restantes</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                    <div>
                                        <p class="font-medium text-green-700">Stock suffisant</p>
                                        <p class="text-sm text-green-600">{{ $product->stock_quantity }} unités disponibles</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images du produit -->
            @if($product->images->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-images mr-2 text-purple-500"></i>
                            Images du produit
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($product->images->take(6) as $image)
                                <a href="{{ asset('storage/' . $image->image_path) }}" 
                                   target="_blank"
                                   class="relative group">
                                    <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-100">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        @if($image->is_primary)
                                            <div class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                                                <i class="fas fa-crown"></i>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @if($product->images->count() > 6)
                            <p class="text-center text-sm text-gray-500 mt-4">
                                + {{ $product->images->count() - 6 }} autres images
                            </p>
                        @endif
                    </div>
                </div>
            @endif

     <!-- Statistiques par statut -->
@if($ordersData['status_stats']->count() > 0)
    <div class="mt-6">
        <h4 class="text-md font-semibold text-gray-700 mb-4">Répartition par statut</h4>
        <div class="space-y-4">
            @foreach($ordersData['status_stats'] as $statusStat)
                @php
                    $statusConfig = match($statusStat->status) {
                        'pending' => ['color' => 'bg-yellow-500', 'icon' => 'fas fa-clock', 'label' => 'En attente'],
                        'preparing' => ['color' => 'bg-blue-500', 'icon' => 'fas fa-utensils', 'label' => 'En préparation'],
                        'out_for_delivery' => ['color' => 'bg-purple-500', 'icon' => 'fas fa-truck', 'label' => 'En livraison'],
                        'delivered' => ['color' => 'bg-green-500', 'icon' => 'fas fa-check-circle', 'label' => 'Livré'],
                        'cancelled' => ['color' => 'bg-red-500', 'icon' => 'fas fa-times-circle', 'label' => 'Annulé'],
                        default => ['color' => 'bg-gray-500', 'icon' => 'fas fa-question-circle', 'label' => 'Inconnu'],
                    };
                    
                    // Calculer les pourcentages basés sur TOUTES les commandes
                    $percentageAll = $ordersData['total_all_orders'] > 0 
                        ? round(($statusStat->order_count / $ordersData['total_all_orders']) * 100, 1)
                        : 0;
                        
                    // Calculer les pourcentages basés sur les commandes VALIDES uniquement
                    $totalValidOrders = $ordersData['total_all_orders'] - $ordersData['cancelled_orders_count'];
                    $percentageValid = $totalValidOrders > 0 && $statusStat->status !== 'cancelled'
                        ? round(($statusStat->order_count / $totalValidOrders) * 100, 1)
                        : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full {{ $statusConfig['color'] }} mr-2"></div>
                            <span class="text-sm font-medium text-gray-700">{{ $statusConfig['label'] }}</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">{{ $statusStat->total_quantity }} unités</span>
                            <span class="text-sm font-bold text-gray-900">{{ $statusStat->order_count }}</span>
                            @if($statusStat->status !== 'cancelled')
                                <span class="text-sm font-bold text-green-600">{{ number_format($statusStat->total_revenue, 2) }} DH</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $statusConfig['color'] }}" style="width: {{ $percentageAll }}%"></div>
                        </div>
                        <span class="text-xs text-gray-500 w-12">{{ $percentageAll }}%</span>
                    </div>
                    @if($statusStat->status === 'cancelled')
                        <div class="text-xs text-red-500 mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Non inclus dans les statistiques de vente
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        
        <!-- Légende -->
        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center text-sm text-gray-600">
                <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                <span class="mr-4">Inclus dans les ventes</span>
                <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                <span>Exclu des ventes</span>
            </div>
        </div>
    </div>
@endif

            <!-- Informations rapides -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl border border-gray-200 p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle mr-2 text-gray-500"></i>
                    Informations rapides
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Catégorie</span>
                        <span class="font-medium text-gray-900">{{ $product->category->name ?? 'Non catégorisé' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Période d'activité</span>
                        <span class="font-medium text-gray-900">{{ $ordersData['activity_period'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Moyenne/commande</span>
                        <span class="font-medium text-gray-900">{{ $ordersData['avg_quantity_per_order'] }} unités</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Valeur moyenne</span>
                        <span class="font-medium text-gray-900">{{ number_format($ordersData['average_order_value'] ?? 0, 2) }} DH</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript pour les interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmation avant suppression
    const deleteForms = document.querySelectorAll('form[data-confirm]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm');
            if (confirm(message)) {
                this.submit();
            }
        });
    });

    // Animation des cartes au survol
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.classList.add('shadow-md');
            card.classList.remove('shadow-sm');
        });
        card.addEventListener('mouseleave', () => {
            card.classList.remove('shadow-md');
            card.classList.add('shadow-sm');
        });
    });
});
</script>

<style>
/* Styles personnalisés */
.aspect-square {
    aspect-ratio: 1 / 1;
}

/* Animation pour les images */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Style pour les liens */
a:hover {
    transition: color 0.2s ease;
}

/* Style pour les tableaux */
table {
    border-collapse: separate;
    border-spacing: 0;
}

table th {
    font-weight: 600;
}

table tr {
    transition: background-color 0.2s ease;
}

/* Barre de progression */
.h-2 {
    height: 0.5rem;
}
</style>
@endsection
                </div>
            </div>

            <!-- Dernières commandes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-receipt mr-2 text-green-500"></i>
                            Dernières commandes
                        </h3>
                        @if($ordersData['total_orders'] > 0)
                            <span class="text-sm text-gray-500">{{ $ordersData['total_orders'] }} commande(s) au total</span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    @if($ordersData['recent_orders']->count() > 0)
                        <div class="overflow-hidden rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Commande</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($ordersData['recent_orders'] as $orderItem)
                                        @php
                                            $order = $orderItem->order;
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($order)
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" 
                                                       class="text-blue-600 hover:text-blue-900 font-medium inline-flex items-center">
                                                        {{ $order->order_number }}
                                                        <i class="fas fa-external-link-alt ml-2 text-xs"></i>
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $orderItem->created_at->format('d/m/Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $orderItem->created_at->format('H:i') }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                    {{ $orderItem->quantity }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($orderItem->price, 2) }} DH</div>
                                                @if($orderItem->discount_price && $orderItem->discount_price < $orderItem->price)
                                                    <div class="text-xs text-green-600">
                                                        <s>{{ number_format($orderItem->price, 2) }} DH</s>
                                                        → {{ number_format($orderItem->discount_price, 2) }} DH
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ number_format($orderItem->subtotal, 2) }} DH
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($order)
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $order->status_color }}">
                                                        {{ $order->status_label }}
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                        N/A
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($ordersData['total_orders'] > 10)
                            <div class="mt-4 text-center">
                                <a href="{{ route('admin.orders.index', ['product_id' => $product->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                    <i class="fas fa-list mr-2"></i>
                                    Voir toutes les commandes ({{ $ordersData['total_orders'] }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Aucune commande</h4>
                            <p class="text-gray-500">Ce produit n'a pas encore été commandé.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Colonne droite : Informations complémentaires -->
        <div class="space-y-6">
            <!-- Stock et inventaire -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-boxes mr-2 text-red-500"></i>
                        Stock & Inventaire
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <!-- Niveau de stock -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Niveau de stock</span>
                                <span class="text-lg font-bold text-gray-900">{{ $product->stock_quantity }}</span>
                            </div>
                            <!-- Barre de progression -->
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $stockPercentage = $product->stock_quantity > 0 
                                        ? min(100, ($product->stock_quantity / 100) * 100)
                                        : 0;
                                    
                                    if($product->stock_quantity == 0) {
                                        $stockColor = 'bg-red-500';
                                    } elseif($product->stock_quantity <= $product->low_stock_alert) {
                                        $stockColor = 'bg-amber-500';
                                    } else {
                                        $stockColor = 'bg-green-500';
                                    }
                                @endphp
                                <div class="h-2 rounded-full {{ $stockColor }}" style="width: {{ $stockPercentage }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">0</span>
                                <span class="text-xs text-gray-500">100+</span>
                            </div>
                        </div>

                        <!-- Alerte stock faible -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alerte stock faible</label>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-exclamation-triangle text-amber-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->low_stock_alert }} unités</p>
                                    <p class="text-xs text-gray-500">Alerte déclenchée en dessous de ce niveau</p>
                                </div>
                            </div>
                        </div>

                        <!-- État du stock -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">État actuel</label>
                            @if($product->stock_quantity == 0)
                                <div class="flex items-center p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <i class="fas fa-times-circle text-red-500 mr-3"></i>
                                    <div>
                                        <p class="font-medium text-red-700">Rupture de stock</p>
                                        <p class="text-sm text-red-600">Le produit est épuisé</p>
                                    </div>
                                </div>
                            @elseif($product->stock_quantity <= $product->low_stock_alert)
                                <div class="flex items-center p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                    <i class="fas fa-exclamation-triangle text-amber-500 mr-3"></i>
                                    <div>
                                        <p class="font-medium text-amber-700">Stock faible</p>
                                        <p class="text-sm text-amber-600">Seulement {{ $product->stock_quantity }} unités restantes</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                    <div>
                                        <p class="font-medium text-green-700">Stock suffisant</p>
                                        <p class="text-sm text-green-600">{{ $product->stock_quantity }} unités disponibles</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images du produit -->
            @if($product->images->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-images mr-2 text-purple-500"></i>
                            Images du produit
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($product->images->take(6) as $image)
                                <a href="{{ asset('storage/' . $image->image_path) }}" 
                                   target="_blank"
                                   class="relative group">
                                    <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-100">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        @if($image->is_primary)
                                            <div class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                                                <i class="fas fa-crown"></i>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @if($product->images->count() > 6)
                            <p class="text-center text-sm text-gray-500 mt-4">
                                + {{ $product->images->count() - 6 }} autres images
                            </p>
                        @endif
                    </div>
                </div>
            @endif
            

            <!-- Statistiques par statut -->
            @if($ordersData['status_stats']->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-pie mr-2 text-indigo-500"></i>
                            Répartition par statut
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($ordersData['status_stats'] as $statusStat)
                                @php
                                    $statusConfig = match($statusStat->status) {
                                        'pending' => ['color' => 'bg-yellow-500', 'icon' => 'fas fa-clock', 'label' => 'En attente'],
                                        'preparing' => ['color' => 'bg-blue-500', 'icon' => 'fas fa-utensils', 'label' => 'En préparation'],
                                        'out_for_delivery' => ['color' => 'bg-purple-500', 'icon' => 'fas fa-truck', 'label' => 'En livraison'],
                                        'delivered' => ['color' => 'bg-green-500', 'icon' => 'fas fa-check-circle', 'label' => 'Livré'],
                                        'cancelled' => ['color' => 'bg-red-500', 'icon' => 'fas fa-times-circle', 'label' => 'Annulé'],
                                        default => ['color' => 'bg-gray-500', 'icon' => 'fas fa-question-circle', 'label' => 'Inconnu'],
                                    };
                                    $percentage = $ordersData['total_orders'] > 0 
                                        ? round(($statusStat->order_count / $ordersData['total_orders']) * 100, 1)
                                        : 0;
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full {{ $statusConfig['color'] }} mr-2"></div>
                                            <span class="text-sm font-medium text-gray-700">{{ $statusConfig['label'] }}</span>
                                        </div>
                                        <span class="text-sm font-bold text-gray-900">{{ $statusStat->order_count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $statusConfig['color'] }}" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        <span class="text-xs text-gray-500">{{ $statusStat->total_quantity }} unités</span>
                                        <span class="text-xs text-gray-500">{{ $percentage }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Informations rapides -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl border border-gray-200 p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle mr-2 text-gray-500"></i>
                    Informations rapides
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Catégorie</span>
                        <span class="font-medium text-gray-900">{{ $product->category->name ?? 'Non catégorisé' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Période d'activité</span>
                        <span class="font-medium text-gray-900">{{ $ordersData['activity_period'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Moyenne/commande</span>
                        <span class="font-medium text-gray-900">{{ $ordersData['avg_quantity_per_order'] }} unités</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Valeur moyenne</span>
                        <span class="font-medium text-gray-900">{{ number_format($ordersData['average_order_value'] ?? 0, 2) }} DH</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript pour les interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmation avant suppression
    const deleteForms = document.querySelectorAll('form[data-confirm]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm');
            if (confirm(message)) {
                this.submit();
            }
        });
    });

    // Animation des cartes au survol
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.classList.add('shadow-md');
            card.classList.remove('shadow-sm');
        });
        card.addEventListener('mouseleave', () => {
            card.classList.remove('shadow-md');
            card.classList.add('shadow-sm');
        });
    });
});
</script>


