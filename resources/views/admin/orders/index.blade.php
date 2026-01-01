@extends('admin.layouts.app')

@section('title', 'Orders - Admin')
@section('header', 'Gestion des Commandes')

@section('content')
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex space-x-4">
            <select name="status" class="px-4 py-2 border rounded-lg" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>En préparation</option>
                <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>En livraison</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livré</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
            </select>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Commande</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
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

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endsection