
<?php
// resources/views/checkout/success.blade.php
?>
@extends('layouts.app')

@section('title', 'Commande confirmée')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-green-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check text-green-500 text-5xl"></i>
        </div>
        
        <h1 class="text-3xl font-bold mb-4">Commande confirmée !</h1>
        <p class="text-gray-600 mb-2">Merci pour votre commande</p>
        <p class="text-2xl font-bold text-green-600 mb-8">N° {{ $order->order_number }}</p>

 <div class="bg-white p-8 rounded-lg shadow-lg mb-8">
    <h2 class="font-bold text-xl mb-4">Détails de la commande</h2>
    
    <div class="text-left space-y-2 mb-6">
        <p><strong>Client:</strong> {{ $order->customer_name }}</p>
        <p><strong>Téléphone:</strong> {{ $order->customer_phone }}</p>
        <p><strong>Adresse:</strong> {{ $order->customer_address }}, {{ $order->customer_city }}</p>
        
        <div class="border-t border-b my-4 py-4">
            @php
            $threshold = settings('free_delivery_threshold');
            $delivery_fee = $threshold && $order->total > $threshold ? 0 : $order->delivery_fee;
            @endphp
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Sous-total:</span>
                    <span>{{ $order->total }} DH</span>
                </div>
                <div class="flex justify-between">
                    <span>Livraison:</span>
                    @if($threshold && $order->total > $threshold)
                    <span class="text-green-600 font-bold">Gratuite</span>
                    @else
                    <span>{{ $order->delivery_fee }} DH</span>
                    @endif
                </div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t">
                    <span>Total:</span>
                    <span class="text-green-600">{{ $order->total + $delivery_fee }} DH</span>
                </div>
            </div>
        </div>
        
        <p><strong>Statut:</strong> 
            <span class=" bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">
                En attente
            </span>
        </p>
    </div>

            <div class="border-t pt-4">
                <h3 class="font-semibold mb-3">Articles commandés</h3>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                        <div class="flex justify-between">
                            <span>{{ $item->product_name }} x{{ $item->quantity }}</span>
                            <span>{{ $item->subtotal }} DH</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-x-4">
            <a href="{{ route('home') }}" 
               class="inline-block bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600">
                Retour à l'accueil
            </a>
            @auth
                <a href="{{ route('orders.index') }}" 
                   class="inline-block bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300">
                    Voir mes commandes
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection