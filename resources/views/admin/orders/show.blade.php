@extends('admin.layouts.app')

@section('title', __('order_details') . ' - Admin')
@section('header', __('order_details') . ' #' . $order->order_number)

@section('content')
@php
    $isFreeDelivery = (float) $order->delivery_fee === 0.0;
    $statusOrder = ['pending', 'preparing', 'out_for_delivery', 'delivered'];
    $currentStatusIndex = array_search($order->status, $statusOrder, true);
    $statusStyles = [
        'pending' => ['badge' => 'bg-amber-50 text-amber-700 ring-amber-200', 'dot' => 'bg-amber-500', 'icon' => 'fa-clock'],
        'preparing' => ['badge' => 'bg-blue-50 text-blue-700 ring-blue-200', 'dot' => 'bg-blue-500', 'icon' => 'fa-box-open'],
        'out_for_delivery' => ['badge' => 'bg-violet-50 text-violet-700 ring-violet-200', 'dot' => 'bg-violet-500', 'icon' => 'fa-truck-fast'],
        'delivered' => ['badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'dot' => 'bg-emerald-500', 'icon' => 'fa-circle-check'],
        'cancelled' => ['badge' => 'bg-red-50 text-red-700 ring-red-200', 'dot' => 'bg-red-500', 'icon' => 'fa-circle-xmark'],
    ];
    $currentStatusStyle = $statusStyles[$order->status] ?? ['badge' => 'bg-gray-50 text-gray-700 ring-gray-200', 'dot' => 'bg-gray-500', 'icon' => 'fa-circle-info'];
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-3 sm:px-5 lg:px-6">
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm" role="status">
            <i class="fas fa-circle-check text-emerald-500" aria-hidden="true"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Page heading and primary actions --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <a href="{{ route('admin.orders.index') }}" class="mb-3 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-emerald-700">
                    <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
                    {{ __('back_to_orders_list') }}
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="font-mono text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl" dir="ltr">#{{ $order->order_number }}</h1>
                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold ring-1 ring-inset {{ $currentStatusStyle['badge'] }}">
                        <span class="h-2 w-2 rounded-full {{ $currentStatusStyle['dot'] }}"></span>
                        {{ $order->status_label }}
                    </span>
                </div>
                <p class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500">
                    <span><i class="far fa-calendar mr-1.5" aria-hidden="true"></i>{{ $order->created_at->format('d/m/Y') }}</span>
                    <span><i class="far fa-clock mr-1.5" aria-hidden="true"></i>{{ $order->created_at->format('H:i') }}</span>
                    <span><i class="fas fa-cubes-stacked mr-1.5" aria-hidden="true"></i>{{ $order->items->sum('quantity') }} article(s)</span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:flex">
                <a href="tel:{{ $order->customer_phone }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-phone text-emerald-600" aria-hidden="true"></i> Appeler
                </a>
                <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    <i class="fas fa-print" aria-hidden="true"></i> Facture
                </a>
            </div>
        </div>

        @if($order->status === 'cancelled')
            <div class="flex items-start gap-3 border-t border-red-100 bg-red-50 px-5 py-4 text-sm text-red-800 sm:px-6">
                <i class="fas fa-circle-xmark mt-0.5" aria-hidden="true"></i>
                <p><strong>Commande annulée.</strong> Le stock des articles a été restauré.</p>
            </div>
        @else
            <div class="border-t border-slate-100 bg-slate-50/70 px-5 py-5 sm:px-6">
                <ol class="grid grid-cols-4" aria-label="Progression de la commande">
                    @foreach($statusOrder as $index => $status)
                        @php
                            $isCompleted = $currentStatusIndex !== false && $index <= $currentStatusIndex;
                            $isCurrent = $index === $currentStatusIndex;
                        @endphp
                        <li class="relative flex flex-col items-center text-center">
                            @if(!$loop->first)
                                <span class="absolute right-1/2 top-4 h-0.5 w-full {{ $isCompleted ? 'bg-emerald-500' : 'bg-slate-200' }}" aria-hidden="true"></span>
                            @endif
                            <span class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 text-xs {{ $isCompleted ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 bg-white text-slate-400' }}" @if($isCurrent) aria-current="step" @endif>
                                <i class="fas {{ $isCompleted && !$isCurrent ? 'fa-check' : $statusStyles[$status]['icon'] }}" aria-hidden="true"></i>
                            </span>
                            <span class="mt-2 hidden text-xs font-semibold sm:block {{ $isCurrent ? 'text-emerald-700' : 'text-slate-500' }}">{{ __('status_' . $status) }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </section>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <main class="space-y-6">
            {{-- Ordered products --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="items-heading">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div>
                        <h2 id="items-heading" class="text-lg font-bold text-slate-900">{{ __('ordered_items') }}</h2>
                        <p class="mt-0.5 text-sm text-slate-500">Détail des produits de cette commande</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $order->items->count() }} ligne(s)</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <article class="grid gap-4 p-4 sm:grid-cols-[5rem_minmax(0,1fr)_auto] sm:items-center sm:px-6 sm:py-5">
                            @if($item->product && $item->product->primaryImage)
                                <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" alt="{{ $item->display_name }}" class="h-20 w-20 rounded-xl border border-slate-100 object-cover">
                            @else
                                <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                                    <i class="fas fa-image text-xl" aria-hidden="true"></i>
                                </div>
                            @endif

                            <div class="min-w-0">
                                <h3 class="font-semibold leading-snug text-slate-900">{{ $item->display_name }}</h3>
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                    <span class="rounded-md bg-slate-100 px-2 py-1 font-semibold text-slate-600">Qté : {{ $item->quantity }}</span>
                                    <span class="text-slate-500">{{ number_format($item->discount_price ?? $item->price, 2) }} DH / unité</span>
                                    @if($item->discount_price)
                                        <span class="text-slate-400 line-through">{{ number_format($item->price, 2) }} DH</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-end justify-between border-t border-slate-100 pt-3 sm:block sm:border-0 sm:pt-0 sm:text-right">
                                <span class="text-xs font-medium text-slate-500 sm:block">Sous-total</span>
                                <span class="text-lg font-bold text-slate-900">{{ number_format($item->subtotal, 2) }} <small class="text-xs font-semibold text-slate-500">DH</small></span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- Customer and delivery details --}}
            <section class="grid gap-6 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><i class="fas fa-user" aria-hidden="true"></i></span>
                        <div><h2 class="font-bold text-slate-900">Informations client</h2><p class="text-xs text-slate-500">Contact de la commande</p></div>
                    </div>
                    <dl class="space-y-4 text-sm">
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Nom complet</dt><dd class="mt-1 font-semibold text-slate-800">{{ $order->customer_name }}</dd></div>
                        <div class="border-t border-slate-100 pt-4"><dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Téléphone</dt><dd class="mt-1"><a href="tel:{{ $order->customer_phone }}" class="font-semibold text-emerald-700 hover:underline" dir="ltr">{{ $order->customer_phone }}</a></dd></div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-location-dot" aria-hidden="true"></i></span>
                        <div><h2 class="font-bold text-slate-900">Livraison</h2><p class="text-xs text-slate-500">Adresse de destination</p></div>
                    </div>
                    <p class="text-sm font-semibold leading-6 text-slate-800">{{ $order->customer_address }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $order->customer_city }}</p>
                    <a href="https://maps.google.com/?q={{ urlencode($order->customer_address . ', ' . $order->customer_city) }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:underline">
                        Voir sur Google Maps <i class="fas fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </section>

            @if($order->notes)
                <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6" aria-labelledby="notes-heading">
                    <div class="flex gap-3"><i class="fas fa-note-sticky mt-1 text-amber-600" aria-hidden="true"></i><div><h2 id="notes-heading" class="font-bold text-amber-900">Notes du client</h2><p class="mt-2 whitespace-pre-line text-sm leading-6 text-amber-900/80">{{ $order->notes }}</p></div></div>
                </section>
            @endif
        </main>

        <aside class="space-y-6 lg:sticky lg:top-6">
            {{-- Status management --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="status-heading">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-arrows-rotate" aria-hidden="true"></i></span>
                    <div><h2 id="status-heading" class="font-bold text-slate-900">{{ __('update_status') }}</h2><p class="text-xs text-slate-500">Mettre à jour le suivi</p></div>
                </div>
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="mt-5 space-y-3">
                    @csrf
                    @method('PUT')
                    <label for="order-status" class="sr-only">{{ __('update_status') }}</label>
                    <select id="order-status" name="status" class="min-h-11 w-full rounded-xl border-slate-300 bg-white text-sm font-medium text-slate-700 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="pending" @selected($order->status === 'pending')>{{ __('status_pending') }}</option>
                        <option value="preparing" @selected($order->status === 'preparing')>{{ __('status_preparing') }}</option>
                        <option value="out_for_delivery" @selected($order->status === 'out_for_delivery')>{{ __('status_out_for_delivery') }}</option>
                        <option value="delivered" @selected($order->status === 'delivered')>{{ __('status_delivered') }}</option>
                        <option value="cancelled" @selected($order->status === 'cancelled')>{{ __('status_cancelled') }}</option>
                    </select>
                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <i class="fas fa-check" aria-hidden="true"></i> Enregistrer le statut
                    </button>
                    <p class="text-center text-xs text-slate-400">Le stock est ajusté automatiquement en cas d'annulation.</p>
                </form>
            </section>

            {{-- Payment summary --}}
            <section class="overflow-hidden rounded-2xl bg-slate-900 text-white shadow-lg" aria-labelledby="summary-heading">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div><h2 id="summary-heading" class="font-bold">Résumé du paiement</h2><p class="mt-0.5 text-xs text-slate-400">{{ __('payment_on_delivery') }}</p></div>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-emerald-300"><i class="fas fa-money-bill-wave" aria-hidden="true"></i></span>
                    </div>

                    <dl class="mt-6 space-y-3 text-sm">
                        <div class="flex justify-between gap-4 text-slate-300"><dt>Sous-total</dt><dd class="font-semibold text-white">{{ number_format($order->subtotal, 2) }} DH</dd></div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between gap-4 text-emerald-300"><dt>Réduction</dt><dd class="font-semibold">-{{ number_format($order->discount_amount, 2) }} DH</dd></div>
                        @endif
                        <div class="flex justify-between gap-4 text-slate-300"><dt>Livraison</dt><dd class="font-semibold {{ $isFreeDelivery ? 'text-emerald-300' : 'text-white' }}">{{ $isFreeDelivery ? 'Gratuite' : number_format($order->delivery_fee, 2) . ' DH' }}</dd></div>
                        <div class="mt-4 flex items-end justify-between gap-4 border-t border-white/10 pt-4"><dt class="font-semibold">Total</dt><dd class="text-2xl font-extrabold tracking-tight text-emerald-300">{{ number_format($order->total, 2) }} <small class="text-sm">DH</small></dd></div>
                    </dl>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
