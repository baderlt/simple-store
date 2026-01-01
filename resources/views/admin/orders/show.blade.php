@extends('admin.layouts.app')

@section('title', 'Order Details - Admin')
@section('header', 'Détails de la Commande #' . $order->order_number)

@section('content')
<div class="container mx-auto px-4">
    {{-- Status Alert --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-500"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-8">
        {{-- Main Order Section --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Order Items --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Articles Commandés</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200">
                                @if($item->product && $item->product->primaryImage)
                                    <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="w-24 h-24 object-cover rounded-lg shadow-sm">
                                @else
                                    <div class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg flex items-center justify-center shadow-sm">
                                        <i class="fas fa-pills text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                                
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800">{{ $item->product_name }}</h4>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-sm">
                                            Qté: {{ $item->quantity }}
                                        </span>
                                        @if($item->discount_price)
                                            <div class="flex items-center space-x-2">
                                                <span class="text-gray-400 line-through text-sm">{{ number_format($item->price, 2) }} DH</span>
                                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-sm font-semibold">
                                                    -{{ number_format($item->price - $item->discount_price, 2) }} DH
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Prix unitaire</p>
                                    <p class="font-bold text-lg {{ $item->discount_price ? 'text-green-600' : 'text-gray-800' }}">
                                        {{ number_format($item->discount_price ?? $item->price, 2) }} DH
                                    </p>
                                    <div class="mt-2 pt-2 border-t">
                                        <p class="text-sm text-gray-600">Sous-total</p>
                                        <p class="font-bold text-xl text-emerald-700">{{ number_format($item->subtotal, 2) }} DH</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pricing Summary --}}
                    <div class="mt-8 pt-8 border-t">
                        <h4 class="font-bold text-lg mb-4 text-gray-700">Récapitulatif du Prix</h4>
                        <div class="max-w-md ml-auto space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Sous-total articles:</span>
                                <span class="font-semibold">{{ number_format($order->subtotal, 2) }} DH</span>
                            </div>
                            
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Réduction:</span>
                                    <span class="font-semibold text-red-600">-{{ number_format($order->discount_amount, 2) }} DH</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Frais de livraison:</span>
                                @php
                                    $threshold = settings('free_delivery_threshold');
                                    $isFreeDelivery = $threshold && $order->total > $threshold;
                                @endphp
                                @if($isFreeDelivery)
                                    <span class="font-semibold text-green-600">Gratuite</span>
                                @else
                                    <span class="font-semibold">{{ number_format($order->delivery_fee, 2) }} DH</span>
                                @endif
                            </div>
                            
                            <div class="pt-3 border-t">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold">Total à payer:</span>
                                    <span class="text-2xl font-bold text-emerald-700">
                                        @if($isFreeDelivery)
                                            {{ number_format($order->total, 2) }} DH
                                        @else
                                            {{ number_format($order->total + $order->delivery_fee, 2) }} DH
                                        @endif
                                    </span>
                                </div>
                                @if($isFreeDelivery)
                                    <p class="text-sm text-green-600 text-right mt-1">
                                        <i class="fas fa-gift mr-1"></i> Livraison offerte
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Section --}}
        <div class="space-y-8">
            {{-- Order Status Card --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Statut de la Commande</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">N° Commande</p>
                            <p class="font-bold text-lg text-gray-800 font-mono">{{ $order->order_number }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Date & Heure</p>
                            <p class="font-semibold text-gray-700">{{ $order->created_at->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('H:i:s') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Mettre à jour le statut</p>
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="relative">
                                    <select name="status" 
                                            class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg appearance-none bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                            onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                                        <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>👨‍🍳 En préparation</option>
                                        <option value="out_for_delivery" {{ $order->status == 'out_for_delivery' ? 'selected' : '' }}>🚚 En livraison</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>✅ Livré</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ Annulé</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Mode de paiement</p>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-money-bill-wave text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold">Paiement à la livraison</p>
                                    <p class="text-sm text-gray-500">(Cash)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer Info Card --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Informations Client</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Nom complet</p>
                                <p class="font-bold text-gray-800">{{ $order->customer_name }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-phone text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Téléphone</p>
                                <p class="font-semibold text-gray-700">{{ $order->customer_phone }}</p>
                                <a href="tel:{{ $order->customer_phone }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800 mt-1 inline-block">
                                    <i class="fas fa-phone-alt mr-1"></i> Appeler
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-map-marker-alt text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Adresse de livraison</p>
                                <p class="font-semibold text-gray-700">{{ $order->customer_address }}</p>
                                <p class="text-gray-600">{{ $order->customer_city }}</p>
                            </div>
                        </div>
                        
                        @if($order->notes)
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-sticky-note text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Notes du client</p>
                                    <div class="mt-1 p-3 bg-yellow-50 border border-yellow-100 rounded-lg">
                                        <p class="text-sm text-gray-700 italic">{{ $order->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Actions</h3>
                </div>
                <div class="p-6 space-y-4">
                    <a href="{{ route('admin.orders.invoice', $order) }}" 
                       target="_blank"
                       class="flex items-center justify-center w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-4 rounded-lg hover:from-green-600 hover:to-emerald-700 transition duration-200 shadow hover:shadow-lg transform hover:-translate-y-1">
                        <i class="fas fa-print mr-3"></i>
                        <span class="font-semibold">Imprimer Facture</span>
                    </a>
                    
                    <a href="https://maps.google.com/?q={{ urlencode($order->customer_address . ', ' . $order->customer_city) }}" 
                       target="_blank"
                       class="flex items-center justify-center w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-4 rounded-lg hover:from-blue-600 hover:to-blue-700 transition duration-200 shadow hover:shadow-lg transform hover:-translate-y-1">
                        <i class="fas fa-map-marked-alt mr-3"></i>
                        <span class="font-semibold">Voir sur la carte</span>
                    </a>
                    
                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex items-center justify-center w-full bg-gradient-to-r from-gray-500 to-gray-600 text-white py-3 px-4 rounded-lg hover:from-gray-600 hover:to-gray-700 transition duration-200 shadow hover:shadow-lg transform hover:-translate-y-1">
                        <i class="fas fa-arrow-left mr-3"></i>
                        <span class="font-semibold">Retour aux Commandes</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    select option {
        padding: 12px;
        background: white;
    }
    select option:hover {
        background: #f3f4f6;
    }
    select option:checked {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
</style>
@endpush