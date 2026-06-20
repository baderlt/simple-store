@extends('layouts.app')

@section('title', __('cart.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">{{ __('cart.page_heading') }}</h1>

    @if(!empty($cart) && count($cart) > 0)
        <div class="grid md:grid-cols-3 gap-8">
            {{-- Pack Items --}}
            <div class="md:col-span-2">
                @php $subtotal = 0; @endphp
                @foreach($cart as $id => $item)
                    @php
                        $linePrice = $item['final_price'] ?? $item['price'];
                        $subtotal += $linePrice * $item['quantity'];
                        $itemVariants = $variantOptions[$item['id']] ?? [];
                        $modalId = 'variant-modal-' . preg_replace('/[^A-Za-z0-9_-]/', '-', (string) $id);
                    @endphp
                    <div class="bg-white p-4 rounded-2xl shadow mb-4 flex flex-col sm:flex-row sm:items-center gap-4 border border-gray-100">
                        <div class="relative shrink-0">
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}"
                                     alt="{{ $item['display_name'] ?? $item['name'] }}"
                                     loading="lazy"
                                     class="w-24 h-24 object-cover rounded-2xl">
                            @else
                                <div class="w-24 h-24 bg-gray-200 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                            @if(count($itemVariants) > 0)
                                <button type="button"
                                        data-cart-variant-open="{{ $modalId }}"
                                        class="absolute inset-x-2 bottom-2 rounded-xl bg-white/95 px-2 py-1 text-xs font-bold text-emerald-700 shadow-lg ring-1 ring-emerald-100 backdrop-blur hover:bg-emerald-50 transition">
                                    <i class="fas fa-pen-to-square mr-1"></i>{{ __('cart.edit_quantity') }}
                                </button>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="bidi-auto font-semibold text-gray-900"
                                dir="auto">{!! bidi_text($item['display_name'] ?? $item['name']) !!}</h3>
                            @if(!empty($item['selected_attributes']))
                                <p class="bidi-auto text-xs text-gray-500 mt-1"
                                   dir="auto">{{ implode(' / ', $item['selected_attributes']) }}</p>
                            @endif

                            @if(isset($item['has_discount']) && $item['has_discount'])
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-gray-400 line-through text-sm">{{ number_format($item['price'], 2) }} DH</span>
                                    <span class="text-green-600 font-bold">{{ number_format($item['final_price'], 2) }} DH</span>
                                </div>
                            @else
                                <p class="text-green-600 font-bold mt-2">{{ number_format($item['final_price'], 2) }} DH</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="{{ $item['minimum_quantity'] ?? 1 }}"
                                       class="w-16 px-2 py-1 border rounded text-center"
                                       onchange="this.form.submit()">
                            </form>

                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if(count($itemVariants) > 0)
                        <div id="{{ $modalId }}" class="cart-variant-modal fixed inset-0 z-50 hidden" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-950/60 backdrop-blur-sm" data-cart-variant-close></div>
                            <div class="relative mx-auto mt-8 w-[92%] max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl sm:mt-16">
                                <div class="relative bg-gradient-to-br from-emerald-50 to-white p-5">
                                    <button type="button" data-cart-variant-close class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-white text-gray-500 shadow hover:text-gray-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <div class="flex gap-4 pr-10">
                                        @if($item['image'])
                                            <img data-cart-variant-preview src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="h-24 w-24 rounded-2xl object-cover shadow">
                                        @endif
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wide text-emerald-600">{{ __('cart.choose_variant') }}</p>
                                            <h2 class="bidi-auto mt-1 text-xl font-black text-gray-900"
                                                dir="auto">{!! bidi_text($item['name']) !!}</h2>
                                            <p class="mt-2 text-sm text-gray-500">{{ __('cart.choose_variant_help') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('cart.add', $item['id']) }}" method="POST" class="space-y-4 p-5" data-cart-variant-form>
                                    @csrf
                                    <input type="hidden" name="variant_id" value="">
                                    <div class="max-h-72 space-y-3 overflow-y-auto pr-1">
                                        @foreach($itemVariants as $variant)
                                            <label class="cart-variant-option flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 p-3 transition hover:border-emerald-400 hover:bg-emerald-50"
                                                   data-price="{{ number_format($variant['final_price'], 2) }}"
                                                   data-image="{{ $variant['image'] }}"
                                                   data-minimum="{{ $variant['minimum_quantity'] }}">
                                                <input type="radio" name="variant_choice" value="{{ $variant['id'] }}" class="h-5 w-5 text-emerald-600 focus:ring-emerald-500">
                                                @if($variant['image'])
                                                    <img src="{{ $variant['image'] }}" alt="{{ $variant['label'] }}" loading="lazy" class="h-14 w-14 rounded-xl object-cover">
                                                @endif
                                                <span class="min-w-0 flex-1">
                                                    <span class="bidi-auto block truncate font-bold text-gray-900"
                                                          dir="auto">{!! bidi_text($variant['label']) !!}</span>
                                                    <span class="text-xs text-gray-500">{{ __('cart.stock_available', ['stock' => $variant['stock_quantity']]) }}</span>
                                                </span>
                                                <span class="text-right font-black text-emerald-600">{{ number_format($variant['final_price'], 2) }} DH</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="flex items-center justify-between gap-3 rounded-2xl bg-gray-50 p-3">
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500">{{ __('cart.selected_price') }}</p>
                                            <p class="text-2xl font-black text-gray-900"><span data-cart-variant-price>—</span> DH</p>
                                        </div>
                                        <input type="number" name="quantity" value="1" min="1" class="w-20 rounded-xl border-gray-300 text-center font-bold focus:border-emerald-500 focus:ring-emerald-500">
                                    </div>

                                    <button type="submit" disabled class="w-full rounded-2xl bg-gray-300 py-4 font-black text-white transition enabled:bg-emerald-600 enabled:hover:bg-emerald-700" data-cart-variant-submit>
                                        <i class="fas fa-box-open mr-2"></i>{{ __('cart.choose_variant_first') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Order Summary --}}
            <div>
                <div class="bg-white p-6 rounded-lg shadow sticky top-24">
                    <h2 class="font-bold text-xl mb-4">{{ __('cart.order_summary') }}</h2>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between"><span>{{ __('cart.subtotal_label') }}</span><span>{{ number_format($subtotal, 2) }} DH</span></div>
                        <div class="flex justify-between"><span>{{ __('cart.delivery') }}</span><span>{{ __('cart.calculated_at_checkout') }}</span></div>
                    </div>
                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between font-bold text-xl"><span>Total</span><span class="text-green-600">{{ number_format($subtotal, 2) }} DH</span></div>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="block w-full bg-green-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-green-600 transition">{{ __('cart.checkout') }}</a>
                    <a href="{{ route('products.index') }}" class="block w-full text-center mt-4 text-green-600 hover:text-green-700">{{ __('cart.continue_shopping') }}</a>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16">
            <i class="fas fa-box-open text-8xl text-gray-300 mb-6"></i>
            <h2 class="text-2xl font-bold mb-4">{{ __('cart.empty_title') }}</h2>
            <a href="{{ route('products.index') }}" class="inline-block bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600">{{ __('cart.view_products') }}</a>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-cart-variant-open]').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = document.getElementById(button.dataset.cartVariantOpen);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        });
    });

    document.querySelectorAll('.cart-variant-modal').forEach((modal) => {
        const close = () => {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        };

        modal.querySelectorAll('[data-cart-variant-close]').forEach((button) => button.addEventListener('click', close));

        const form = modal.querySelector('[data-cart-variant-form]');
        if (!form) return;

        form.querySelectorAll('input[name="variant_choice"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                const option = radio.closest('.cart-variant-option');
                form.querySelector('input[name="variant_id"]').value = radio.value;
                form.querySelector('input[name="quantity"]').min = option.dataset.minimum || 1;
                form.querySelector('input[name="quantity"]').value = option.dataset.minimum || 1;
                modal.querySelector('[data-cart-variant-price]').textContent = option.dataset.price;
                const preview = modal.querySelector('[data-cart-variant-preview]');
                if (preview && option.dataset.image) preview.src = option.dataset.image;
                form.querySelectorAll('.cart-variant-option').forEach((label) => label.classList.remove('border-emerald-500', 'bg-emerald-50', 'ring-2', 'ring-emerald-100'));
                option.classList.add('border-emerald-500', 'bg-emerald-50', 'ring-2', 'ring-emerald-100');
                const submit = form.querySelector('[data-cart-variant-submit]');
                submit.disabled = false;
                submit.innerHTML = '<i class="fas fa-box-open mr-2"></i>{{ __('cart.add_to_pack') }}';
            });
        });
    });
});
</script>
@endsection
