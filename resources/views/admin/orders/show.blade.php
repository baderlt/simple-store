@extends('admin.layouts.app')

@section('title', __('order_details') . ' - Admin')
@section('header', __('order_details') . ' #' . $order->order_number)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-2">

    {{-- Back Button (mobile) --}}
    <div class="mb-5 lg:hidden">
        <a href="{{ route('admin.orders.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
            <i class="fas fa-arrow-left text-xs"></i>
            {{ __('back_to_orders_list') }}
        </a>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 px-4 py-3 rounded-lg mb-5 text-sm shadow-sm">
            <i class="fas fa-check-circle text-emerald-500 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @php
        $isFreeDelivery = (float) $order->delivery_fee === 0.0;
        $statusStyles = [
            'pending'          => ['pill' => 'bg-amber-100 text-amber-800',   'icon' => 'fa-clock'],
            'preparing'        => ['pill' => 'bg-blue-100 text-blue-800',     'icon' => 'fa-kitchen-set'],
            'out_for_delivery' => ['pill' => 'bg-violet-100 text-violet-800', 'icon' => 'fa-truck-fast'],
            'delivered'        => ['pill' => 'bg-emerald-100 text-emerald-800','icon' => 'fa-circle-check'],
            'cancelled'        => ['pill' => 'bg-red-100 text-red-800',       'icon' => 'fa-circle-xmark'],
        ];
        $s = $statusStyles[$order->status] ?? ['pill' => 'bg-gray-100 text-gray-700', 'icon' => 'fa-circle-info'];
    @endphp

    {{-- ── Hero Banner ── --}}
    <section class="bg-gray-900 rounded-2xl px-6 py-5 mb-6 grid grid-cols-2 sm:grid-cols-4 gap-4 items-center"
             aria-labelledby="order-hero-heading">
        {{-- Order ID --}}
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-widest text-emerald-400 mb-1 flex items-center gap-1.5">
                <i class="fas fa-receipt" aria-hidden="true"></i>{{ __('order_details') }}
            </p>
            <p id="order-hero-heading" class="text-[11px] text-gray-400 mb-0.5">{{ __('order_number') }}</p>
            <p dir="ltr" class="font-mono font-bold text-white text-base tracking-tight">{{ $order->order_number }}</p>
        </div>

        {{-- Date --}}
        <div class="bg-gray-800 rounded-xl px-4 py-3">
            <p class="text-[10px] text-gray-400 mb-1.5">{{ __('Date') }}</p>
            <p dir="ltr" class="text-sm font-semibold text-gray-100">{{ $order->created_at->format('d/m/Y') }}</p>
            <p dir="ltr" class="text-[11px] text-gray-500 mt-0.5">{{ $order->created_at->format('H:i') }}</p>
        </div>

        {{-- Status --}}
        <div class="bg-gray-800 rounded-xl px-4 py-3">
            <p class="text-[10px] text-gray-400 mb-2">{{ __('order_status_section') }}</p>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $s['pill'] }}">
                <i class="fas {{ $s['icon'] }} shrink-0" aria-hidden="true"></i>
                {{ $order->status_label }}
            </span>
        </div>

        {{-- Total --}}
        <div class="bg-emerald-900/60 border border-emerald-700/40 rounded-xl px-4 py-3">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-emerald-400 mb-1">Total</p>
            <p dir="ltr" class="text-2xl font-extrabold text-white tracking-tight leading-none">
                {{ number_format($order->total, 2) }} DH
            </p>
            @if($isFreeDelivery)
                <span class="inline-flex items-center gap-1 mt-2 px-2 py-0.5 rounded-full bg-emerald-800/60 text-emerald-300 text-[10px] font-semibold">
                    <i class="fas fa-gift" aria-hidden="true"></i>{{ __('free_delivery') }}
                </span>
            @endif
        </div>
    </section>

    {{-- ── Body Grid ── --}}
    <div class="grid lg:grid-cols-[1fr_320px] gap-6">

        {{-- ── Left: Items + Pricing ── --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-5 py-4 flex items-center gap-3">
                    <i class="fas fa-basket-shopping text-white/80" aria-hidden="true"></i>
                    <h2 class="text-base font-semibold text-white">{{ __('ordered_items') }}</h2>
                </div>

                <div class="p-5 space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4 p-3 bg-gray-50 hover:bg-gray-100 border border-transparent hover:border-gray-200 rounded-xl transition-all duration-150">
                            {{-- Image --}}
                            @if($item->product && $item->product->primaryImage)
                                <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}"
                                     alt="{{ $item->display_name }}"
                                     class="w-14 h-14 object-cover rounded-lg shrink-0 shadow-sm">
                            @else
                                <div class="w-14 h-14 bg-gray-200 rounded-lg shrink-0 flex items-center justify-center">
                                    <i class="fas fa-basket-shopping text-gray-400"></i>
                                </div>
                            @endif

                            {{-- Name + qty --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->display_name }}</p>
                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                    <span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded-full text-xs font-medium">
                                        Qté: {{ $item->quantity }}
                                    </span>
                                    @if($item->discount_price)
                                        <span class="text-gray-400 line-through text-xs">{{ number_format($item->price, 2) }} DH</span>
                                        <span class="px-2 py-0.5 bg-red-50 text-red-600 rounded-full text-xs font-medium border border-red-100">
                                            −{{ number_format($item->price - $item->discount_price, 2) }} DH
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Prices --}}
                            <div class="text-right shrink-0">
                                <p class="text-sm font-semibold {{ $item->discount_price ? 'text-emerald-600' : 'text-gray-800' }}">
                                    {{ number_format($item->discount_price ?? $item->price, 2) }} DH
                                </p>
                                <p class="text-base font-bold text-emerald-700 mt-1">{{ number_format($item->subtotal, 2) }} DH</p>
                            </div>
                        </div>
                    @endforeach

                    {{-- Pricing recap --}}
                    <div class="pt-4 mt-2 border-t border-gray-100">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">Récapitulatif du Prix</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Sous-total articles:</span>
                                <span class="font-medium text-gray-800">{{ number_format($order->subtotal, 2) }} DH</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Réduction:</span>
                                    <span class="font-medium text-red-600">−{{ number_format($order->discount_amount, 2) }} DH</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-500">Frais de livraison:</span>
                                @if($isFreeDelivery)
                                    <span class="font-medium text-emerald-600">Gratuite</span>
                                @else
                                    <span class="font-medium text-gray-800">{{ number_format($order->delivery_fee, 2) }} DH</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">
                            <span class="text-sm font-bold text-gray-800">Total à payer:</span>
                            <span class="text-xl font-extrabold text-emerald-700">{{ number_format($order->total, 2) }} DH</span>
                        </div>
                        @if($isFreeDelivery)
                            <p class="text-xs text-emerald-600 text-right mt-1">
                                <i class="fas fa-gift mr-1" aria-hidden="true"></i>{{ __('free_delivery') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Right Sidebar ── --}}
        <div class="space-y-5">

            {{-- Status Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-5 py-4 flex items-center gap-3">
                    <i class="fas fa-rotate text-white/80" aria-hidden="true"></i>
                    <h2 class="text-base font-semibold text-white">{{ __('order_status_section') }}</h2>
                </div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-[11px] text-gray-400 mb-0.5">{{ __('order_number') }}</p>
                            <p class="font-mono font-bold text-gray-800 text-sm">{{ $order->order_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] text-gray-400 mb-0.5">{{ __('Date') }}</p>
                            <p class="font-semibold text-gray-700 text-sm">{{ $order->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <p class="text-[11px] text-gray-400 mb-2">{{ __('update_status') }}</p>
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="relative">
                            <select name="status"
                                    class="w-full pl-4 pr-10 py-2.5 text-sm font-medium border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent appearance-none transition"
                                    onchange="this.form.submit()">
                                <option value="pending"          {{ $order->status == 'pending'          ? 'selected' : '' }}>⏳ {{ __('status_pending') }}</option>
                                <option value="preparing"        {{ $order->status == 'preparing'        ? 'selected' : '' }}>👨‍🍳 {{ __('status_preparing') }}</option>
                                <option value="out_for_delivery" {{ $order->status == 'out_for_delivery' ? 'selected' : '' }}>🚚 {{ __('status_out_for_delivery') }}</option>
                                <option value="delivered"        {{ $order->status == 'delivered'        ? 'selected' : '' }}>✅ {{ __('status_delivered') }}</option>
                                <option value="cancelled"        {{ $order->status == 'cancelled'        ? 'selected' : '' }}>❌ {{ __('status_cancelled') }}</option>
                            </select>
                            <i class="fas fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs" aria-hidden="true"></i>
                        </div>
                    </form>

                    <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-100">
                        <div class="w-9 h-9 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-money-bill-wave text-emerald-600" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ __('payment_on_delivery') }}</p>
                            <p class="text-xs text-gray-400">(Cash)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-violet-500 to-purple-600 px-5 py-4 flex items-center gap-3">
                    <i class="fas fa-user text-white/80" aria-hidden="true"></i>
                    <h2 class="text-base font-semibold text-white">Informations Client</h2>
                </div>
                <div class="p-5 divide-y divide-gray-50 space-y-3">
                    <div class="flex items-start gap-3 pb-3">
                        <div class="w-9 h-9 bg-violet-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-violet-600 text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400">Nom complet</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $order->customer_name }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 pt-3 pb-3">
                        <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-phone text-blue-600 text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400">Téléphone</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $order->customer_phone }}</p>
                            <a href="tel:{{ $order->customer_phone }}"
                               class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-flex items-center gap-1">
                                <i class="fas fa-phone-alt text-[10px]" aria-hidden="true"></i>Appeler
                            </a>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 pt-3 {{ $order->notes ? 'pb-3' : '' }}">
                        <div class="w-9 h-9 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-map-marker-alt text-emerald-600 text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400">Adresse de livraison</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $order->customer_address }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $order->customer_city }}</p>
                        </div>
                    </div>
                    @if($order->notes)
                        <div class="flex items-start gap-3 pt-3">
                            <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                                <i class="fas fa-sticky-note text-amber-500 text-sm" aria-hidden="true"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] text-gray-400 mb-1.5">Notes du client</p>
                                <div class="bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 text-xs text-amber-900 italic leading-relaxed">
                                    {{ $order->notes }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-5 py-4 flex items-center gap-3">
                    <i class="fas fa-bolt text-white/80" aria-hidden="true"></i>
                    <h2 class="text-base font-semibold text-white">Actions</h2>
                </div>
                <div class="p-4 space-y-2.5">
                    <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
                       class="flex items-center justify-center gap-2.5 w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[.98] text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition-all">
                        <i class="fas fa-print" aria-hidden="true"></i>
                        Imprimer Facture
                    </a>
                    <a href="https://maps.google.com/?q={{ urlencode($order->customer_address . ', ' . $order->customer_city) }}" target="_blank"
                       class="flex items-center justify-center gap-2.5 w-full bg-indigo-600 hover:bg-indigo-700 active:scale-[.98] text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition-all">
                        <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
                        Voir sur la carte
                    </a>
                    <a href="{{ route('admin.orders.index') }}"
                       class="flex items-center justify-center gap-2.5 w-full bg-gray-100 hover:bg-gray-200 active:scale-[.98] text-gray-700 text-sm font-semibold py-2.5 px-4 rounded-xl transition-all">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        {{ __('back_to_orders') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
select option { padding: 10px; }
@media (max-width: 640px) {
    .grid { grid-template-columns: 1fr; }
}
</style>
@endpush
