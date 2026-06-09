@extends('admin.layouts.app')

@section('title', $product->name . ' - Admin')
@section('header', 'Détails du produit')
@section('subheader', $product->name)

@section('content')
<<<<<<< HEAD
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
=======
@php
    $primaryImage = $product->images->firstWhere('is_primary', true) ?: $product->images->first();
    $activeVariants = $product->variants->where('is_active', true);
    $variantStock = $activeVariants->sum('stock_quantity');
    $displayStock = $product->variants->isNotEmpty() ? $variantStock : $product->stock_quantity;
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.products.index') }}" class="hover:text-amber-600">Produits</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="truncate">{{ $product->name }}</span>
>>>>>>> 81d327b3579da3498952892c9f613052ecd50127
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <div class="flex flex-wrap gap-2 mt-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $product->is_active ? 'Actif' : 'Inactif' }}
                </span>
                @if($product->is_featured)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700"><i class="fas fa-star mr-1"></i>En vedette</span>
                @endif
                @if($product->variants->isNotEmpty())
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">{{ $product->variants->count() }} variantes</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-external-link-alt mr-2"></i>Voir la boutique
            </a>
            <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-white bg-amber-500 hover:bg-amber-600 shadow-sm">
                <i class="fas fa-edit mr-2"></i>Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Prix de base</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($product->price, 2) }} DH</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Stock disponible</p>
            <p class="text-2xl font-bold {{ $displayStock <= $product->low_stock_alert ? 'text-red-600' : 'text-green-600' }} mt-1">{{ $displayStock }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Quantité vendue</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $ordersData['total_quantity'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Chiffre d’affaires</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($ordersData['total_revenue'], 2) }} DH</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
        <div class="xl:col-span-2 space-y-6 min-w-0">
            <section class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">Informations du produit</h2>
                </div>
                <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-5">
                        <div><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Catégorie</p><p class="mt-1 font-medium text-gray-900">{{ $product->category?->name ?? 'Sans catégorie' }}</p></div>
                        <div><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">SKU</p><p class="mt-1 font-medium text-gray-900">{{ $product->sku ?: 'Non défini' }}</p></div>
                        <div><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Alerte stock faible</p><p class="mt-1 font-medium text-gray-900">{{ $product->low_stock_alert }} unités</p></div>
                    </div>
                    <div class="space-y-5">
                        <div><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Créé le</p><p class="mt-1 text-gray-700">{{ $product->created_at->format('d/m/Y à H:i') }}</p></div>
                        <div><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Modifié le</p><p class="mt-1 text-gray-700">{{ $product->updated_at->format('d/m/Y à H:i') }}</p></div>
                        <div><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Période d’activité</p><p class="mt-1 text-gray-700">{{ $ordersData['activity_period'] }}</p></div>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Description</p>
                        <div class="mt-2 text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->description ?: 'Aucune description.' }}</div>
                    </div>
                </div>
            </section>

            @if($product->variants->isNotEmpty())
                <section class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-gray-100 flex items-center justify-between gap-3">
                        <div><h2 class="text-lg font-bold text-gray-900">Variantes</h2><p class="text-sm text-gray-500">Prix, stock et options disponibles</p></div>
                        <span class="text-sm font-semibold text-purple-700 bg-purple-50 px-3 py-1 rounded-full">{{ $activeVariants->count() }} actives</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50"><tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Options</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">SKU</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Prix</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Stock</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($product->variants as $variant)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-4"><div class="font-medium text-gray-900">{{ $variant->option_label ?: 'Variante' }}</div>@if($variant->unit)<div class="text-xs text-gray-500 mt-1">Unité : {{ $variant->unit }}</div>@endif</td>
                                        <td class="px-5 py-4 text-sm text-gray-600">{{ $variant->sku ?: '—' }}</td>
                                        <td class="px-5 py-4 font-semibold text-gray-900">{{ number_format($variant->price, 2) }} DH</td>
                                        <td class="px-5 py-4"><span class="font-semibold {{ $variant->stock_quantity <= $product->low_stock_alert ? 'text-red-600' : 'text-green-600' }}">{{ $variant->stock_quantity }}</span></td>
                                        <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $variant->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $variant->is_active ? 'Active' : 'Inactive' }}</span>@if($variant->is_default)<span class="ml-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Défaut</span>@endif</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            <section class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div><h2 class="text-lg font-bold text-gray-900">Dernières commandes</h2><p class="text-sm text-gray-500">{{ $ordersData['total_orders'] }} commande(s) non annulée(s)</p></div>
                    <div class="text-sm text-gray-500">Annulation : {{ $ordersData['cancellation_rate'] }}%</div>
                </div>
                @if($ordersData['recent_orders']->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50"><tr><th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Commande</th><th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Variante</th><th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Qté</th><th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th><th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($ordersData['recent_orders'] as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-4"><a class="font-semibold text-blue-600 hover:text-blue-800" href="{{ route('admin.orders.show', $item->order) }}">{{ $item->order?->order_number }}</a><div class="text-xs text-gray-500 mt-1">{{ $item->created_at->format('d/m/Y H:i') }}</div></td>
                                        <td class="px-5 py-4 text-sm text-gray-600">{{ $item->variant_snapshot ? implode(' / ', array_values($item->variant_snapshot)) : 'Produit standard' }}</td>
                                        <td class="px-5 py-4 font-semibold">{{ $item->quantity }}</td>
                                        <td class="px-5 py-4 font-semibold">{{ number_format($item->subtotal, 2) }} DH</td>
                                        <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->order?->status_color ?? 'bg-gray-100 text-gray-700' }}">{{ $item->order?->status_label ?? 'N/A' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-10 text-center text-gray-500"><i class="fas fa-receipt text-3xl text-gray-300 mb-3"></i><p>Aucune commande pour ce produit.</p></div>
                @endif
            </section>
        </div>

        <aside class="space-y-6 min-w-0">
            <section class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="aspect-square bg-gray-100">
                    @if($primaryImage)
                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-image text-6xl"></i></div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between"><h2 class="font-bold text-gray-900">Galerie</h2><span class="text-sm text-gray-500">{{ $product->images->count() }}/10</span></div>
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-4 gap-2 mt-4">
                            @foreach($product->images as $image)
                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank" class="relative aspect-square rounded-lg overflow-hidden border {{ $image->is_primary ? 'border-amber-500 ring-2 ring-amber-200' : 'border-gray-200' }}">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="" class="w-full h-full object-cover">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h2 class="font-bold text-gray-900 mb-4">Résumé des ventes</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Commandes</span><span class="font-semibold">{{ $ordersData['total_orders'] }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Moyenne / commande</span><span class="font-semibold">{{ $ordersData['avg_quantity_per_order'] }} unités</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Valeur moyenne</span><span class="font-semibold">{{ number_format($ordersData['average_order_value'] ?? 0, 2) }} DH</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Commandes annulées</span><span class="font-semibold text-red-600">{{ $ordersData['cancelled_orders_count'] }}</span></div>
                </div>
            </section>

            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Supprimer définitivement ce produit ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl border border-red-200 bg-red-50 text-red-700 font-semibold hover:bg-red-100"><i class="fas fa-trash mr-2"></i>Supprimer le produit</button>
            </form>
        </aside>
    </div>
</div>
@endsection
<<<<<<< HEAD
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


=======
>>>>>>> 81d327b3579da3498952892c9f613052ecd50127
