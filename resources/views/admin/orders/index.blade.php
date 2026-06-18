@extends('admin.layouts.app')

@section('title', __('orders_management') . ' - Admin')
@section('header', __('orders_management'))

@section('content')

<div class="mb-5 bg-white rounded-2xl border border-gray-200 shadow-sm p-3">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-3">

        {{-- Main filters --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-2 items-center">
            <div class="relative lg:col-span-8">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('admin.order_search_placeholder') }}"
                       aria-label="{{ __('admin.global_search') }}"
                       class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            </div>

            <select name="status"
                    aria-label="{{ __('admin.status') }}"
                    class="w-full lg:col-span-2 px-3 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                <option value="">{{ __('admin.all_statuses') }}</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('status_pending') }}</option>
                <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>{{ __('status_preparing') }}</option>
                <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>{{ __('status_out_for_delivery') }}</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ __('status_delivered') }}</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('status_cancelled') }}</option>
            </select>

            <button type="submit"
                    class="w-full lg:col-span-1 inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 text-sm font-medium">
                <i class="fas fa-filter mr-2"></i>{{ __('admin.filter') }}
            </button>

            <a href="{{ route('admin.orders.index') }}"
               class="w-full lg:col-span-1 inline-flex items-center justify-center px-3 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 text-sm"
               title="{{ __('admin.reset') }}"
               aria-label="{{ __('admin.reset') }}">
                <i class="fas fa-redo"></i>
            </a>
        </div>

        {{-- Advanced filters --}}
        <details class="group" {{ request('date_from') || request('date_to') || request('min_total') || request('max_total') ? 'open' : '' }}>
            <summary class="cursor-pointer select-none text-xs font-medium text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
                <i class="fas fa-sliders-h"></i>
                {{ __('admin.advanced_filters') }}
            </summary>

            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                <input type="date"
                       name="date_from"
                       value="{{ request('date_from') }}"
                       aria-label="{{ __('admin.date_from') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">

                <input type="date"
                       name="date_to"
                       value="{{ request('date_to') }}"
                       aria-label="{{ __('admin.date_to') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">

                <input type="number"
                       step="0.01"
                       name="min_total"
                       value="{{ request('min_total') }}"
                       placeholder="{{ __('admin.min_total') }}"
                       aria-label="{{ __('admin.min_total') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">

                <input type="number"
                       step="0.01"
                       name="max_total"
                       value="{{ request('max_total') }}"
                       placeholder="{{ __('admin.max_total') }}"
                       aria-label="{{ __('admin.max_total') }}"
                       class="w-full px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            </div>
        </details>

        <p class="text-xs text-gray-500">
            {{ trans_choice('admin.orders_found_database', $orders->total(), ['count' => $orders->total()]) }}
        </p>
    </form>
</div>

@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'preparing' => 'bg-blue-100 text-blue-800',
        'out_for_delivery' => 'bg-purple-100 text-purple-800',
        'delivered' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];

    $statusLabels = [
        'pending' => 'En attente',
        'preparing' => 'En préparation',
        'out_for_delivery' => 'En livraison',
        'delivered' => 'Livré',
        'cancelled' => 'Annulé',
    ];
@endphp

{{-- Desktop table --}}
<div class="hidden md:block bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Commande</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produits</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="font-mono text-sm font-bold text-gray-900">
                                #{{ $order->order_number }}
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900">
                                {{ $order->customer_name }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-phone mr-1"></i>{{ $order->customer_phone }}
                            </div>
                        </td>

                        <td class="px-4 py-4 max-w-xs">
                            <div class="text-sm font-medium text-gray-700">
                                {{ trans_choice('admin.items_count', $order->items->count(), ['count' => $order->items->count()]) }}
                            </div>
                            <div class="text-xs text-gray-500 truncate">
                                {{ $order->items->pluck('product.name')->filter()->take(3)->join(', ') }}
                                @if($order->items->count() > 3)
                                    ...
                                @endif
                            </div>
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="font-bold text-green-600">
                                {{ number_format($order->total, 2) }} DH
                            </span>
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">
                                {{ $order->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $order->created_at->format('H:i') }}
                            </div>
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('admin.orders.invoice', $order) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-green-50 text-green-600 hover:bg-green-100"
                                   title="Imprimer facture">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                            Aucune commande trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Mobile cards --}}
<div class="md:hidden space-y-4">
    @forelse($orders as $order)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
            <div class="flex justify-between items-start gap-3 mb-3">
                <div>
                    <div class="font-mono font-bold text-sm text-gray-900">
                        #{{ $order->order_number }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div class="text-right">
                    <div class="font-bold text-green-600 text-sm">
                        {{ number_format($order->total, 2) }} DH
                    </div>
                    <span class="inline-flex mt-1 px-2 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>
            </div>

            <div class="space-y-2 text-sm">
                <div class="flex items-center text-gray-700">
                    <i class="fas fa-user text-gray-400 w-5"></i>
                    <span class="ml-2">{{ $order->customer_name }}</span>
                </div>

                <div class="flex items-center text-gray-700">
                    <i class="fas fa-phone text-gray-400 w-5"></i>
                    <span class="ml-2">{{ $order->customer_phone }}</span>
                </div>

                <div class="flex items-start text-gray-700">
                    <i class="fas fa-box text-gray-400 w-5 mt-1"></i>
                    <span class="ml-2">
                        {{ trans_choice('admin.items_count', $order->items->count(), ['count' => $order->items->count()]) }}
                        —
                        {{ $order->items->pluck('product.name')->filter()->take(2)->join(', ') }}
                    </span>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.orders.show', $order) }}"
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <i class="fas fa-eye mr-1"></i>
                    {{ __('admin.details') }}
                </a>

                <a href="{{ route('admin.orders.invoice', $order) }}"
                   target="_blank"
                   class="inline-flex items-center text-green-600 hover:text-green-800 text-sm font-medium">
                    <i class="fas fa-file-invoice mr-1"></i>
                    Facture
                </a>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 text-center text-gray-500">
            Aucune commande trouvée.
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>

@endsection
