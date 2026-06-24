
<?php
// resources/views/checkout/success.blade.php
?>
@extends('layouts.app')

@section('title', __('checkout.success_page_title'))
@section('robots', 'noindex, nofollow')

@section('content')
@php
    $currency = __('checkout.currency');
@endphp
<style>
    .order-confirmation-success-icon {
        background-color: #dcfce7 !important;
        border: 2px solid #86efac;
        box-shadow: 0 12px 30px rgba(22, 163, 74, 0.16);
    }

    .order-confirmation-success-icon i {
        color: #16a34a !important;
    }
</style>
<div class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto text-center">
        <div class="order-confirmation-success-icon w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check text-5xl"></i>
        </div>
        
        <h1 class="text-3xl font-bold mb-4">{{ __('checkout.success_heading') }}</h1>
        <p class="mx-auto mb-3 max-w-xl text-lg font-semibold leading-relaxed text-gray-700">
            {{ __('checkout.success_thank_you') }}
        </p>
        <p class="text-2xl font-bold text-green-600 mb-8">N° {{ $order->order_number }}</p>

 <div class="bg-white p-8 rounded-lg shadow-lg mb-8">
    <h2 class="font-bold text-xl mb-4">Détails de la commande</h2>
    
    <div class="text-left space-y-2 mb-6">
        <p><strong>Client:</strong> {{ $order->customer_name }}</p>
        <p><strong>Téléphone:</strong> {{ $order->customer_phone }}</p>
        <p><strong>Adresse:</strong> {{ $order->customer_address }}, {{ $order->customer_city }}</p>
        
        <div class="border-t border-b my-4 py-4">
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Sous-total:</span>
                    <span>{{ number_format($order->subtotal, 2) }} {{ $currency }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Livraison:</span>
                    @if((float) $order->delivery_fee === 0.0)
                    <span class="text-green-600 font-bold">Gratuite</span>
                    @else
                    <span>{{ number_format($order->delivery_fee, 2) }} {{ $currency }}</span>
                    @endif
                </div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t">
                    <span>Total:</span>
                    <span class="text-green-600">{{ number_format($order->total, 2) }} {{ $currency }}</span>
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
                            <span class="bidi-auto" dir="auto">
                                {!! bidi_text($item->display_name) !!}
                                <bdi dir="ltr">× {{ $item->quantity }}</bdi>
                            </span>
                            <span>{{ $item->subtotal }} {{ $currency }}</span>
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
