@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-8">
            <!-- Left Column: Order Summary -->
            <div class="mb-8 lg:mb-0">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-8">
                    @if($isDirect ?? false)
                        <div></div>
                    @else
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">Votre panier</h1>
                            <p class="text-gray-600 mt-1">Vérifiez vos articles avant de commander</p>
                        </div>
                    @endif

                    <!-- Cart Items -->
                    <div class="space-y-4">
                        @foreach($cart as $item)
                            <div class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-emerald-300 transition-colors">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" 
                                         alt="{{ $item['name'] }}" 
                                         loading="lazy"
                                         class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                                
                                <div class="ml-4 flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $item['name'] }}</h4>
                                    <div class="flex items-center justify-between mt-2">
                                        <div class="flex items-center space-x-4">
                                            <span class="text-lg font-bold text-gray-900">
                                                {{ number_format($item['final_price'] * $item['quantity'], 2) }} DH
                                            </span>
                                            @if($item['has_discount'] && $item['final_price'] < $item['price'])
                                                <span class="text-sm text-gray-400 line-through">
                                                    {{ number_format($item['price'] * $item['quantity'], 2) }} DH
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-gray-600">
                                            {{ $item['quantity'] }} × {{ number_format($item['final_price'], 2) }} DH
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Sous-total</span>
                                @php
                                    $subtotal = 0;
                                    foreach($cart as $item) {
                                        $subtotal += $item['final_price'] * $item['quantity'];
                                    }
                                @endphp
                                <span class="font-medium">{{ number_format($subtotal, 2) }} DH</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Frais de livraison</span>
                                <span class="font-medium">{{ number_format($deliveryFee, 2) }} DH</span>
                            </div>
                            @php
                                $threshold=settings('free_delivery_threshold');
                            @endphp
                            @if($threshold && $subtotal > $threshold)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Livraison offerte</span>
                                    <span class="font-medium text-rose-600">-{{ number_format($deliveryFee, 2) }} DH</span>
                                </div>
                            @endif
                            <div class="border-t border-gray-300 pt-3">
                                <div class="flex justify-between text-lg">
                                    <span class="font-bold text-gray-900">Total</span>
                                    <span class="font-bold text-emerald-600">
                                        {{ number_format($subtotal + ($threshold && $subtotal > $threshold ? 0 : $deliveryFee), 2) }} DH
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Checkout Form -->
            <div>
                <div class="bg-white rounded-2xl shadow-lg p-6 relative">
                    <!-- Loading Overlay -->
                    <div id="loadingOverlay" class="hidden absolute inset-0 bg-white bg-opacity-90 z-10 flex flex-col items-center justify-center rounded-2xl">
                        <div class="text-center">
                            <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-emerald-600 mb-4"></div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Traitement de votre commande</h3>
                            <p class="text-gray-600">Veuillez patienter...</p>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Informations de livraison</h2>
                    
                    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Customer Name -->
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom complet *
                                </label>
                                <input type="text" 
                                       name="customer_name" 
                                       id="customer_name"
                                       value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                @error('customer_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Phone -->
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Téléphone *
                                </label>
                                <input type="tel" 
                                       name="customer_phone" 
                                       id="customer_phone"
                                       value="{{ old('customer_phone', auth()->user()->phone ?? '') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                       placeholder="06 XX XX XX XX">
                                @error('customer_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- City -->
                            <div>
                                <label for="customer_city" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ville *
                                </label>
                                <input type="text" name="customer_city" 
                                        id="customer_city"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"/>
                                @error('customer_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Address -->
                            <div>
                                <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Adresse complète *
                                </label>
                                <textarea name="customer_address" 
                                          id="customer_address"
                                          rows="3"
                                          required
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                          placeholder="Numéro, rue, quartier, immeuble...">{{ old('customer_address') }}</textarea>
                                @error('customer_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes supplémentaires (optionnel)
                                </label>
                                <textarea name="notes" 
                                          id="notes"
                                          rows="2"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                          placeholder="Instructions de livraison, codes d'accès, horaires préférés...">{{ old('notes') }}</textarea>
                            </div>
                            
                            <!-- Payment Method -->
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Méthode de paiement</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center p-4 border-2 border-emerald-500 rounded-lg bg-emerald-50">
                                        <input type="radio" 
                                               id="cash_on_delivery" 
                                               name="payment_method" 
                                               value="cash_on_delivery" 
                                               checked
                                               class="h-5 w-5 text-emerald-600 focus:ring-emerald-500">
                                        <label for="cash_on_delivery" class="ml-3 flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="font-medium text-gray-900">Paiement à la livraison</span>
                                                    <p class="text-sm text-gray-600">Payez en espèces lorsque vous recevez votre commande</p>
                                                </div>
                                                <i class="fas fa-money-bill-wave text-emerald-600 text-xl"></i>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="mt-8 md:mt-8 mobile-checkout-submit md:static fixed bottom-0 left-0 right-0 z-50 bg-white p-3 md:p-0 border-t md:border-t-0 shadow-2xl md:shadow-none">
                            <button type="submit" 
                                    id="submitButton"
                                    class="w-full bg-green-600 text-white font-bold py-4 px-6 rounded-lg hover:bg-green-700 transition-all shadow-lg hover:shadow-xl flex items-center justify-center group disabled:opacity-70 disabled:cursor-not-allowed">
                                <span id="buttonText">
                                    @if($isDirect ?? false)
                                        <i class="fas fa-bolt mr-3 group-hover:rotate-12 transition-transform"></i>
                                        Commander maintenant
                                    @else
                                        <i class="fas fa-shopping-cart mr-3 group-hover:rotate-12 transition-transform"></i>
                                        Confirmer la commande
                                    @endif
                                </span>
                                <span id="buttonSpinner" class="hidden">
                                    <i class="fas fa-spinner fa-spin mr-3"></i>
                                    Traitement en cours...
                                </span>
                            </button>
                            
                            <!-- Back link -->
                            <div class="mt-4 text-center">
                                @if($isDirect ?? false)
                                    <a href="{{ route('products.show', reset($cart)['slug'] ?? '#') }}" 
                                       class="inline-flex items-center text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Retour au produit
                                    </a>
                                @else
                                    <a href="{{ route('cart.index') }}" 
                                       class="inline-flex items-center text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Modifier le panier
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-format phone number
    document.getElementById('customer_phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.length <= 2) {
                value = value;
            } else if (value.length <= 4) {
                value = value.substr(0, 2) + ' ' + value.substr(2);
            } else if (value.length <= 6) {
                value = value.substr(0, 2) + ' ' + value.substr(2, 2) + ' ' + value.substr(4);
            } else if (value.length <= 8) {
                value = value.substr(0, 2) + ' ' + value.substr(2, 2) + ' ' + value.substr(4, 2) + ' ' + value.substr(6);
            } else {
                value = value.substr(0, 2) + ' ' + value.substr(2, 2) + ' ' + value.substr(4, 2) + ' ' + value.substr(6, 2);
            }
        }
        e.target.value = value;
    });
    
    // Form submission handling
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        // Prevent multiple submissions
        if (this.classList.contains('submitting')) {
            e.preventDefault();
            return false;
        }
        
        this.classList.add('submitting');
        
        const button = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');
        const buttonSpinner = document.getElementById('buttonSpinner');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        // Show loading states
        button.disabled = true;
        buttonText.classList.add('hidden');
        buttonSpinner.classList.remove('hidden');
        loadingOverlay.classList.remove('hidden');
    
        // Allow form to submit normally - the loading will continue until page reloads
        return true;
    });
    
    // Prevent double-click
    let formSubmitted = false;
    document.getElementById('checkoutForm').addEventListener('submit', function() {
        if (formSubmitted) {
            return false;
        }
        formSubmitted = true;
        return true;
    });
</script>

<style>
    #loadingOverlay {
        backdrop-filter: blur(2px);
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    .submitting {
        animation: pulse 1.5s infinite;
    }
</style>

<style>
    /* mobile checkout fixed padding */
    @media (max-width: 767px) {
        body {
            padding-bottom: 92px;
        }
    }
</style>
@endsection