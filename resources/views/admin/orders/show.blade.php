@extends('admin.layouts.app')

@section('title', __('order_details') . ' - Admin')
@section('header', __('order_details') . ' #' . $order->order_number)

@section('content')
<div class="container mx-auto px-3 sm:px-4">
        {{-- Back to Orders Button --}}
    <div class="mb-4 lg:hidden md:hidden">
        <a href="{{ route('admin.orders.index') }}"
           class="inline-flex items-center text-blue-600 hover:text-blue-800 transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            <span class="font-medium">{{ __('back_to_orders_list') }}</span>
        </a>
    </div>
    {{-- Status Alert --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-500"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Order Summary Mobile Header --}}
    <div class="md:hidden bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-lg font-bold text-gray-800">{{ __('order_number') }}{{ $order->order_number }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'preparing' => 'bg-blue-100 text-blue-800',
                    'out_for_delivery' => 'bg-purple-100 text-purple-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                ];
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $order->status_label }}
            </span>
        </div>

        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-100">
            <div>
                <p class="text-sm text-gray-600">Total</p>
                <p class="text-xl font-bold text-emerald-700">
                    @php($isFreeDelivery = (float) $order->delivery_fee === 0.0)
                    {{ number_format($order->total, 2) }} DH
                </p>
            </div>
            @if($isFreeDelivery)
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                    <i class="fas fa-gift mr-1"></i> {{ __('free_delivery') }}
                </span>
            @endif
        </div>
    </div>

    <div class="lg:grid lg:grid-cols-3 lg:gap-8 space-y-6 lg:space-y-0">
        {{-- Main Order Section --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Items --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-4 sm:px-6 py-4">
                    <h3 class="text-lg sm:text-xl font-bold text-white">{{ __('ordered_items') }}</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 p-3 sm:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200">
                                @if($item->product && $item->product->primaryImage)
                                    <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}"
                                         alt="{{ $item->display_name }}"
                                         class="w-full sm:w-24 h-32 sm:h-24 object-cover rounded-lg shadow-sm">
                                @else
                                    <div class="w-full sm:w-24 h-32 sm:h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg flex items-center justify-center shadow-sm">
                                        <i class="fas fa-basket-shopping text-gray-400 text-2xl"></i>
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800 text-sm sm:text-base">{{ $item->display_name }}</h4>
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs sm:text-sm">
                                            Qté: {{ $item->quantity }}
                                        </span>
                                        @if($item->discount_price)
                                            <div class="flex items-center space-x-1">
                                                <span class="text-gray-400 line-through text-xs sm:text-sm">{{ number_format($item->price, 2) }} DH</span>
                                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs sm:text-sm font-semibold">
                                                    -{{ number_format($item->price - $item->discount_price, 2) }} DH
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="sm:text-right pt-3 sm:pt-0 border-t sm:border-t-0">
                                    <div class="flex justify-between sm:block">
                                        <span class="text-xs text-gray-600 sm:hidden">Prix unitaire:</span>
                                        <p class="font-bold text-base sm:text-lg {{ $item->discount_price ? 'text-green-600' : 'text-gray-800' }}">
                                            {{ number_format($item->discount_price ?? $item->price, 2) }} DH
                                        </p>
                                    </div>
                                    <div class="mt-2 pt-2 border-t flex justify-between sm:block">
                                        <span class="text-xs text-gray-600 sm:hidden">Sous-total:</span>
                                        <p class="font-bold text-lg sm:text-xl text-emerald-700">{{ number_format($item->subtotal, 2) }} DH</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pricing Summary --}}
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="font-bold text-lg mb-4 text-gray-700">Récapitulatif du Prix</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm sm:text-base">Sous-total articles:</span>
                                <span class="font-semibold">{{ number_format($order->subtotal, 2) }} DH</span>
                            </div>

                            @if($order->discount_amount > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm sm:text-base">Réduction:</span>
                                    <span class="font-semibold text-red-600">-{{ number_format($order->discount_amount, 2) }} DH</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm sm:text-base">Frais de livraison:</span>
                                @if($isFreeDelivery)
                                    <span class="font-semibold text-green-600">Gratuite</span>
                                @else
                                    <span class="font-semibold">{{ number_format($order->delivery_fee, 2) }} DH</span>
                                @endif
                            </div>

                            <div class="pt-3 border-t">
                                <div class="flex justify-between items-center">
                                    <span class="text-base sm:text-lg font-bold">Total à payer:</span>
                                    <span class="text-xl sm:text-2xl font-bold text-emerald-700">
                                        {{ number_format($order->total, 2) }} DH
                                    </span>
                                </div>
                                @if($isFreeDelivery)
                                    <p class="text-xs sm:text-sm text-green-600 text-right mt-1">
                                        <i class="fas fa-gift mr-1"></i> {{ __('free_delivery') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Section --}}
        <div class="space-y-6">
            {{-- Order Status Card --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 sm:px-6 py-4">
                    <h3 class="text-lg sm:text-xl font-bold text-white">{{ __('order_status_section') }}</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600 mb-1">{{ __('order_number') }}</p>
                                <p class="font-bold text-base sm:text-lg text-gray-800 font-mono">{{ $order->order_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs sm:text-sm text-gray-600 mb-1">{{ __('Date') }}</p>
                                <p class="font-semibold text-gray-700">{{ $order->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <div class="pt-3 border-t">
                            <p class="text-xs sm:text-sm text-gray-600 mb-2">{{ __('update_status') }}</p>
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="relative">
                                    <select name="status"
                                            class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg appearance-none bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm sm:text-base"
                                            onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ {{ __('status_pending') }}</option>
                                        <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>👨‍🍳 {{ __('status_preparing') }}</option>
                                        <option value="out_for_delivery" {{ $order->status == 'out_for_delivery' ? 'selected' : '' }}>🚚 {{ __('status_out_for_delivery') }}</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>✅ {{ __('status_delivered') }}</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ {{ __('status_cancelled') }}</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="pt-3 border-t">
                            <p class="text-xs sm:text-sm text-gray-600 mb-1">{{ __('payment_method') }}</p>
                            <div class="flex items-center">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-money-bill-wave text-green-600 text-sm sm:text-base"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm sm:text-base">{{ __('payment_on_delivery') }}</p>
                                    <p class="text-xs sm:text-sm text-gray-500">(Cash)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer Info Card --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-4 sm:px-6 py-4">
                    <h3 class="text-lg sm:text-xl font-bold text-white">Informations Client</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-user text-purple-600 text-sm sm:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm text-gray-600">Nom complet</p>
                                <p class="font-bold text-gray-800 text-sm sm:text-base truncate">{{ $order->customer_name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start pt-3 border-t">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-phone text-blue-600 text-sm sm:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm text-gray-600">Téléphone</p>
                                <p class="font-semibold text-gray-700 text-sm sm:text-base">{{ $order->customer_phone }}</p>
                                <a href="tel:{{ $order->customer_phone }}"
                                   class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 mt-1 inline-flex items-center">
                                    <i class="fas fa-phone-alt mr-1"></i> Appeler
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start pt-3 border-t">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-emerald-600 text-sm sm:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm text-gray-600">Adresse de livraison</p>
                                <p class="font-semibold text-gray-700 text-sm sm:text-base">{{ $order->customer_address }}</p>
                                <p class="text-gray-600 text-xs sm:text-sm">{{ $order->customer_city }}</p>
                            </div>
                        </div>

                        @if($order->notes)
                            <div class="flex items-start pt-3 border-t">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-sticky-note text-yellow-600 text-sm sm:text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm text-gray-600">Notes du client</p>
                                    <div class="mt-1 p-3 bg-yellow-50 border border-yellow-100 rounded-lg">
                                        <p class="text-xs sm:text-sm text-gray-700 italic">{{ $order->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-4 sm:px-6 py-4">
                    <h3 class="text-lg sm:text-xl font-bold text-white">Actions</h3>
                </div>
                <div class="p-4 sm:p-6 space-y-3">
                    <a href="{{ route('admin.orders.invoice', $order) }}"
                       target="_blank"
                       class="flex items-center justify-center w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-4 rounded-lg hover:from-green-600 hover:to-emerald-700 transition duration-200 shadow hover:shadow-lg active:scale-[0.98]">
                        <i class="fas fa-print mr-2 sm:mr-3"></i>
                        <span class="font-semibold text-sm sm:text-base">Imprimer Facture</span>
                    </a>

                    <a href="https://maps.google.com/?q={{ urlencode($order->customer_address . ', ' . $order->customer_city) }}"
                       target="_blank"
                       class="flex items-center justify-center w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-4 rounded-lg hover:from-blue-600 hover:to-blue-700 transition duration-200 shadow hover:shadow-lg active:scale-[0.98]">
                        <i class="fas fa-map-marked-alt mr-2 sm:mr-3"></i>
                        <span class="font-semibold text-sm sm:text-base">Voir sur la carte</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}"
                       class="flex items-center justify-center w-full bg-gradient-to-r from-gray-500 to-gray-600 text-white py-3 px-4 rounded-lg hover:from-gray-600 hover:to-gray-700 transition duration-200 shadow hover:shadow-lg active:scale-[0.98]">
                        <i class="fas fa-arrow-left mr-2 sm:mr-3"></i>
                        <span class="font-semibold text-sm sm:text-base">{{ __('back_to_orders') }}</span>
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

    /* Improve mobile scrolling */
    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Better touch targets */
        a, button, select {
            min-height: 44px;
        }
    }
</style>
@endpush
