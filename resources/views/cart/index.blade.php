@extends('layouts.app')

@section('title', __('cart.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">{{ __('cart.page_heading') }}</h1>

    @if(!empty($cart) && count($cart) > 0)
        <div class="grid md:grid-cols-3 gap-8">
            {{-- Pack Items --}}
            <div class="md:col-span-2">
                @php
                    $subtotal = 0;
                @endphp
                @foreach($cart as $id => $item)
                    @php
                        $subtotal += $item['final_price'] * $item['quantity'];
                    @endphp
                    <div class="bg-white p-4 rounded-lg shadow mb-4 flex items-center gap-4">
                        @if($item['image'])
                            <img src="{{ asset('storage/' . $item['image']) }}"
                                 alt="{{ $item['display_name'] ?? $item['name'] }}"
                                 loading="lazy"
                                 class="w-20 h-20 object-cover rounded">
                        @else
                            <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif

                        <div class="flex-1">
                            <h3 class="font-semibold">{{ $item['display_name'] ?? $item['name'] }}</h3>
                            @if(!empty($item['selected_attributes']))
                                <p class="text-xs text-gray-500 mt-1">{{ implode(' / ', $item['selected_attributes']) }}</p>
                            @endif

                            {{-- Show discount if applicable --}}
                            @if(isset($item['has_discount']) && $item['has_discount'])
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-400 line-through text-sm">{{ number_format($item['price'], 2) }} DH</span>
                                    <span class="text-green-600 font-bold">{{ number_format($item['final_price'], 2) }} DH</span>
                                </div>
                            @else
                                <p class="text-green-600 font-bold">{{ number_format($item['final_price'], 2) }} DH</p>
                            @endif
                        </div>

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
                @endforeach
            </div>

            {{-- Order Summary --}}
            <div>
                <div class="bg-white p-6 rounded-lg shadow sticky top-24">
                    <h2 class="font-bold text-xl mb-4">{{ __('cart.order_summary') }}</h2>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span>{{ __('cart.subtotal_label') }}</span>
                            <span>{{ number_format($subtotal, 2) }} DH</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ __('cart.delivery') }}</span>
                            <span>{{ __('cart.calculated_at_checkout') }}</span>
                        </div>
                    </div>

                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between font-bold text-xl">
                            <span>Total</span>
                            <span class="text-green-600">{{ number_format($subtotal, 2) }} DH</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}"
                       class="block w-full bg-green-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-green-600 transition">
                        {{ __('cart.checkout') }}
                    </a>

                    <a href="{{ route('products.index') }}"
                       class="block w-full text-center mt-4 text-green-600 hover:text-green-700">
                        {{ __('cart.continue_shopping') }}
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16">
            <i class="fas fa-box-open text-8xl text-gray-300 mb-6"></i>
            <h2 class="text-2xl font-bold mb-4">{{ __('cart.empty_title') }}</h2>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600">
                {{ __('cart.view_products') }}
            </a>
        </div>
    @endif
</div>
@endsection
