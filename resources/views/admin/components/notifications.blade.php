<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button type="button"
            @click="open = !open"
            :aria-expanded="open"
            aria-haspopup="true"
            aria-label="{{ __('new_order_notifications') }}"
            class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
        <i class="far fa-bell"></i>
        @if($newOrdersCount > 0)
            <span class="absolute -top-1 -right-1 flex min-h-5 min-w-5 items-center justify-center rounded-full border-2 border-white bg-red-500 px-1 text-[10px] font-bold leading-none text-white">
                {{ $newOrdersCount > 99 ? '99+' : $newOrdersCount }}
            </span>
        @endif
    </button>

    <div x-cloak
         x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="notification-panel absolute right-0 z-50 mt-3 w-[min(22rem,calc(100vw-2rem))] origin-top-right overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white px-4 py-3">
            <div>
                <p class="font-bold text-gray-900">{{ __('new_orders') }}</p>
                <p class="text-xs text-gray-500">{{ __('pending_orders_only') }}</p>
            </div>
            @if($newOrdersCount > 0)
                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">{{ $newOrdersCount }}</span>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($newOrders as $order)
                <a href="{{ route('admin.orders.show', $order) }}" class="flex gap-3 border-b border-gray-100 px-4 py-3 transition last:border-0 hover:bg-gray-50">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <i class="fas fa-bag-shopping"></i>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center justify-between gap-2">
                            <strong class="truncate text-sm text-gray-900" dir="ltr">#{{ $order->order_number }}</strong>
                            <span class="shrink-0 text-[11px] text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                        </span>
                        <span class="mt-0.5 block truncate text-sm text-gray-600">{{ $order->customer_name }}</span>
                        <span class="mt-1 block text-xs font-bold text-emerald-700">{{ number_format($order->total, 2) }} DH</span>
                    </span>
                </a>
            @empty
                <div class="px-6 py-10 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-500">
                        <i class="fas fa-check"></i>
                    </span>
                    <p class="mt-3 font-semibold text-gray-800">{{ __('no_new_orders') }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ __('new_orders_will_appear_here') }}</p>
                </div>
            @endforelse
        </div>

        @if($newOrdersCount > 0)
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="block border-t border-gray-100 px-4 py-3 text-center text-sm font-bold text-emerald-700 transition hover:bg-emerald-50">
                {{ __('view_all_new_orders') }}
            </a>
        @endif
    </div>
</div>
