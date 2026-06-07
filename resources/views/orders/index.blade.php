@extends('layouts.app')

@section('title', 'Mes Commandes')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Mes Commandes</h1>

    @if($orders->count() > 0)
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg">Commande #{{ $order->order_number }}</h3>
                            <p class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
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
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$order->status] }}">
                                {{ $statusLabels[$order->status] }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center space-x-4">
                                    @if($item->product && $item->product->primaryImage)
                                        <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}"
                                             alt="{{ $item->display_name }}"
                                             loading="lazy"
                                             class="w-20 h-20 object-cover rounded">
                                    @else
                                        <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <h4 class="font-semibold">{{ $item->display_name }}</h4>
                                        <p class="text-sm text-gray-600">Quantité: {{ $item->quantity }}</p>
                                        <p class="text-sm text-green-600 font-semibold">
                                            {{ $item->discount_price ?? $item->price }} DH x {{ $item->quantity }}
                                        </p>
                                    </div>

                                    <div class="text-right">
                                        <p class="font-bold text-lg">{{ $item->subtotal }} DH</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-6 border-t">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold mb-2">Adresse de livraison</h4>
                                    <p class="text-gray-600">{{ $order->customer_name }}</p>
                                    <p class="text-gray-600">{{ $order->customer_address }}</p>
                                    <p class="text-gray-600">{{ $order->customer_city }}</p>
                                    <p class="text-gray-600 mt-1">{{ $order->customer_phone }}</p>
                                </div>

                                <div class="space-y-2 text-right">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Sous-total:</span>
                                        <span class="font-semibold">{{ $order->total }} DH</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Livraison:</span>
                                        @php
                                            $threshold = settings('free_delivery_threshold');
                                        @endphp
                                        @if($threshold && $order->total > $threshold)
                                            <span class="text-green-600 font-semibold">Gratuite</span>
                                        @else
                                            <span class="font-semibold">{{ $order->delivery_fee }} DH</span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between text-lg pt-2 border-t">
                                        <span class="font-bold">Total:</span>
                                        @if($threshold && $order->total > $threshold)
                                            <span class="text-2xl font-bold text-green-600">{{ $order->total }} DH</span>
                                        @else
                                            <span class="text-2xl font-bold text-green-600">{{ $order->total + $order->delivery_fee }} DH</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <i class="fas fa-shopping-bag text-8xl text-gray-300 mb-6"></i>
            <h2 class="text-2xl font-bold mb-4">Aucune commande</h2>
            <p class="text-gray-600 mb-6">Vous n'avez pas encore passé de commande</p>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600">
                Découvrir nos produits
            </a>
        </div>
    @endif
</div>
@endsection
