@extends('admin.layouts.app')

@section('title', __('orders_management') . ' - Admin')
@section('header', __('orders_management'))

@section('content')
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.orders.index') }}">
            <select name="status" class="w-full sm:w-auto px-4 py-2 border rounded-lg" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('status_pending') }}</option>
                <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>{{ __('status_preparing') }}</option>
                <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>{{ __('status_out_for_delivery') }}</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ __('status_delivered') }}</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('status_cancelled') }}</option>
            </select>
        </form>
    </div>

    <!-- Desktop Table (hidden on mobile) -->
    <div class="bg-white rounded-lg shadow overflow-hidden hidden md:block">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('order_number') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('customer') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Téléphone') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Statut') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-semibold">{{ $order->order_number }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4">{{ $order->customer_phone }}</td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-green-600">{{ number_format($order->total, 2) }} DH</span>
                        </td>
                        <td class="px-6 py-4">
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
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="text-blue-600 hover:text-blue-800" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.orders.invoice', $order) }}" 
                                   class="text-green-600 hover:text-green-800" 
                                   target="_blank" title="Imprimer facture">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Card Layout (hidden on desktop) -->
    <div class="md:hidden space-y-4">
        @foreach($orders as $order)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-mono font-semibold text-sm text-gray-900">
                            #{{ $order->order_number }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-semibold text-green-600 text-sm">
                            {{ number_format($order->total, 2) }} DH
                        </span>
                        <div class="mt-1">
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
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-user text-gray-400 w-5"></i>
                        <span class="ml-2">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 w-5"></i>
                        <span class="ml-2">{{ $order->customer_phone }}</span>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-4 pt-4 border-t">
                    <a href="{{ route('admin.orders.show', $order) }}" 
                       class="text-blue-600 hover:text-blue-800 flex items-center text-sm"
                       title="Voir détails">
                        <i class="fas fa-eye mr-1"></i>
                        <span>Détails</span>
                    </a>
                    <a href="{{ route('admin.orders.invoice', $order) }}" 
                       class="text-green-600 hover:text-green-800 flex items-center text-sm"
                       target="_blank" title="Imprimer facture">
                        <i class="fas fa-file-invoice mr-1"></i>
                        <span>Facture</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endsection