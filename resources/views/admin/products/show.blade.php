@extends('admin.layouts.app')

@section('title', $product->name . ' - Admin')
@section('header', 'Détails du produit')
@section('subheader', $product->name)

@section('content')
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
