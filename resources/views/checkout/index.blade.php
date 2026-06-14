@extends('layouts.app')

@section('title', __('checkout.page_title'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8 text-center lg:text-left">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">{{ __('checkout.heading') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('checkout.intro') }}</p>
        </div>

        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <!-- Left Column: Checkout Form (spans 2 columns on desktop) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-6 sm:p-8 relative">
                        <!-- Loading Overlay -->
                        <div id="loadingOverlay" class="hidden absolute inset-0 bg-white/95 backdrop-blur-sm z-20 flex flex-col items-center justify-center rounded-2xl">
                            <div class="text-center p-6">
                                <div class="inline-block animate-spin rounded-full h-14 w-14 border-4 border-emerald-200 border-t-emerald-600 mb-4"></div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('checkout.processing_title') }}</h3>
                                <p class="text-gray-600">{{ __('checkout.processing_message') }}</p>
                            </div>
                        </div>

                        <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm" novalidate>
                            @csrf

                            <div class="space-y-8">
                                <!-- Delivery Information Section -->
                                <div>
                                    <div class="flex items-center space-x-3 mb-6 pb-2 border-b border-gray-200">
                                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-truck text-emerald-600 text-lg"></i>
                                        </div>
                                        <h2 class="text-xl font-bold text-gray-900">{{ __('checkout.delivery_information') }}</h2>
                                    </div>

                                    <label for="is_laayoune_delivery"
                                           class="mb-6 flex cursor-pointer items-start gap-4 rounded-xl border-2 border-emerald-200 bg-emerald-50 p-4 transition-colors hover:border-emerald-400">
                                        <input type="checkbox"
                                               name="is_laayoune_delivery"
                                               id="is_laayoune_delivery"
                                               value="1"
                                               @checked(old('is_laayoune_delivery'))
                                               class="mt-1 h-5 w-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="flex-1">
                                            <span class="flex flex-wrap items-center justify-between gap-2">
                                                <span class="font-bold text-gray-900">{{ __('checkout.laayoune_delivery_title') }}</span>
                                                <span class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white">
                                                    {{ __('checkout.laayoune_delivery_free') }}
                                                </span>
                                            </span>
                                            <span class="mt-1 block text-sm text-gray-600">{{ __('checkout.laayoune_delivery_description') }}</span>
                                            <span class="mt-2 block text-xs font-medium text-gray-500">
                                                {{ __('checkout.other_cities_delivery', ['price' => number_format($deliveryFee, 2)]) }}
                                            </span>
                                        </span>
                                    </label>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Customer Name -->
                                        <div class="md:col-span-2">
                                            <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('checkout.full_name') }} <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="text"
                                                   name="customer_name"
                                                   id="customer_name"
                                                   value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 @error('customer_name') border-rose-500 @enderror"
                                                   placeholder="{{ __('checkout.full_name_placeholder') }}">
                                            @error('customer_name')
                                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Phone -->
                                        <div>
                                            <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('checkout.phone') }} <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="tel"
                                                   name="customer_phone"
                                                   id="customer_phone"
                                                   value="{{ old('customer_phone', auth()->user()->phone ?? '') }}"
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 @error('customer_phone') border-rose-500 @enderror"
                                                   placeholder="{{ __('checkout.phone_placeholder') }}">
                                            @error('customer_phone')
                                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- City -->
                                        <div>
                                            <label for="customer_city" class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('checkout.city') }} <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="text"
                                                   name="customer_city"
                                                   id="customer_city"
                                                   value="{{ old('customer_city') }}"
                                                   list="moroccanCities"
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 @error('customer_city') border-rose-500 @enderror"
                                                   placeholder="{{ __('checkout.city_placeholder') }}">
                                            @error('customer_city')
                                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Address (full width) -->
                                        <div class="md:col-span-2">
                                            <label for="customer_address" class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('checkout.address') }} <span class="text-rose-500">*</span>
                                            </label>
                                            <textarea name="customer_address"
                                                      id="customer_address"
                                                      rows="3"
                                                      required
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 @error('customer_address') border-rose-500 @enderror"
                                                      placeholder="{{ __('checkout.address_placeholder') }}">{{ old('customer_address') }}</textarea>
                                            @error('customer_address')
                                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Notes (optional) -->
                                        <div class="md:col-span-2">
                                            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('checkout.notes') }}
                                            </label>
                                            <textarea name="notes"
                                                      id="notes"
                                                      rows="2"
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                                      placeholder="{{ __('checkout.notes_placeholder') }}">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Method Section - Cash on Delivery only -->
                                <div>
                                    <div class="flex items-center space-x-3 mb-6 pb-2 border-b border-gray-200">
                                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-money-bill-wave text-emerald-600 text-lg"></i>
                                        </div>
                                        <h2 class="text-xl font-bold text-gray-900">{{ __('checkout.payment_method') }}</h2>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="relative">
                                            <input type="radio"
                                                   id="cash_on_delivery"
                                                   name="payment_method"
                                                   value="cash_on_delivery"
                                                   checked
                                                   class="peer hidden">
                                            <label for="cash_on_delivery" 
                                                   class="flex items-center justify-between p-5 border-2 border-emerald-500 rounded-xl cursor-pointer bg-emerald-50">
                                                <div class="flex items-center space-x-4">
                                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm">
                                                        <i class="fas fa-money-bill-wave text-emerald-600 text-xl"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-gray-900">{{ __('checkout.cash_on_delivery') }}</p>
                                                        <p class="text-sm text-gray-500">{{ __('checkout.cash_on_delivery_description') }}</p>
                                                    </div>
                                                </div>
                                                <div class="w-6 h-6 rounded-full border-2 border-emerald-500 bg-emerald-500 flex items-center justify-center">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="text-xs text-gray-500 text-center mt-4">
                                            <i class="fas fa-lock mr-1"></i> {{ __('checkout.fully_secure_payment') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button Section (Desktop) -->
                            <div class="mt-8 pt-6 border-t border-gray-200 hidden md:block">
                                <button type="submit"
                                        id="submitButton"
                                        class="w-full bg-gradient-to-r from-emerald-600 to-green-600 text-white font-bold py-4 px-6 rounded-xl hover:from-emerald-700 hover:to-green-700 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center group disabled:opacity-70 disabled:cursor-not-allowed">
                                    <span id="buttonText">
                                        @if($isDirect ?? false)
                                            <i class="fas fa-bolt mr-3 group-hover:rotate-12 transition-transform"></i>
                                            {{ __('checkout.confirm_and_order') }}
                                        @else
                                            <i class="fas fa-check-circle mr-3 group-hover:scale-110 transition-transform"></i>
                                            {{ __('checkout.confirm_order') }}
                                        @endif
                                    </span>
                                    <span id="buttonSpinner" class="hidden">
                                        <i class="fas fa-spinner fa-spin mr-3"></i>
                                        {{ __('checkout.processing') }}
                                    </span>
                                </button>

                                <div class="mt-4 text-center">
                                    @if($isDirect ?? false)
                                        <a href="{{ route('products.show', reset($cart)['slug'] ?? '#') }}"
                                           class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors">
                                            <i class="fas fa-arrow-left mr-2"></i>
                                            {{ __('checkout.back_to_product') }}
                                        </a>
                                    @else
                                        <a href="{{ route('cart.index') }}"
                                           class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors">
                                            <i class="fas fa-shopping-cart mr-2"></i>
                                            {{ __('checkout.edit_cart') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary (Sticky) -->
            <div class="mt-8 lg:mt-0">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 sticky top-8 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-shopping-bag mr-3 text-emerald-600"></i>
                            {{ __('checkout.order_summary') }}
                        </h2>
                    </div>

                    <!-- Cart Items -->
                    <div class="p-6 space-y-4 max-h-[320px] overflow-y-auto custom-scrollbar">
                        @foreach($cart as $item)
                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-100 last:border-0">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                         alt="{{ $item['display_name'] ?? $item['name'] }}"
                                         loading="lazy"
                                         class="w-16 h-16 object-cover rounded-lg shadow-sm">
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400 text-xl"></i>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 text-sm truncate">{{ $item['display_name'] ?? $item['name'] }}</h4>
                                    @if(!empty($item['selected_attributes']))
                                        <p class="text-xs text-gray-500 mt-1 truncate">{{ implode(' / ', $item['selected_attributes']) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mt-2">
                                        <div class="flex items-baseline space-x-2">
                                            <span class="font-bold text-gray-900 text-sm">
                                                {{ number_format($item['final_price'] * $item['quantity'], 2) }} DH
                                            </span>
                                            @if($item['has_discount'] && $item['final_price'] < $item['price'])
                                                <span class="text-xs text-gray-400 line-through">
                                                    {{ number_format($item['price'] * $item['quantity'], 2) }} DH
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                            {{ __('checkout.quantity_short', ['quantity' => $item['quantity']]) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="p-6 bg-gray-50 border-t border-gray-100">
                        @php
                            $subtotal = 0;
                            foreach($cart as $item) {
                                $subtotal += $item['final_price'] * $item['quantity'];
                            }
                            $threshold = settings('free_delivery_threshold');
                            $deliveryFeeToShow = ($threshold && $subtotal > $threshold) ? 0 : $deliveryFee;
                            $total = $subtotal + $deliveryFeeToShow;
                        @endphp
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ __('checkout.subtotal') }}</span>
                                <span class="font-medium text-gray-900">{{ number_format($subtotal, 2) }} DH</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ __('checkout.delivery_fee') }}</span>
                                <span id="deliveryFeeValue"
                                      class="font-medium {{ $threshold && $subtotal > $threshold ? 'text-emerald-600' : 'text-gray-900' }}">
                                    {{ $threshold && $subtotal > $threshold ? __('checkout.free') : number_format($deliveryFee, 2).' DH' }}
                                </span>
                            </div>

                            @if($threshold && $subtotal > 0 && $subtotal <= $threshold)
                                <div id="freeDeliveryThresholdNotice" class="bg-amber-50 rounded-lg p-3 text-xs text-amber-700">
                                    <i class="fas fa-truck mr-1"></i> 
                                    {{ __('checkout.free_delivery_remaining', ['amount' => number_format($threshold - $subtotal, 2)]) }}
                                </div>
                            @endif

                            <div class="border-t border-gray-200 pt-3 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">{{ __('checkout.total') }}</span>
                                    <span id="checkoutTotal" class="text-2xl font-bold text-emerald-600">
                                        {{ number_format($total, 2) }} DH
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-right">{{ __('checkout.tax_included') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secure Payment Badge -->
                <div class="mt-4 text-center text-xs text-gray-500 flex items-center justify-center space-x-4">
                    <span><i class="fas fa-lock text-emerald-600 mr-1"></i> {{ __('checkout.secure_payment') }}</span>
                    <span><i class="fas fa-shield-alt text-emerald-600 mr-1"></i> {{ __('checkout.protected_data') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sticky Mobile Checkout Button -->
<div id="mobileCheckoutSubmit" 
     class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200 p-4 shadow-[0_-10px_30px_-8px_rgba(0,0,0,0.1)] transition-all duration-500 ease-out md:hidden translate-y-full opacity-0 pointer-events-none">
    <div class="flex items-center justify-between gap-3">
        <div class="flex flex-col">
            <span class="text-xs text-gray-500">{{ __('checkout.total') }}</span>
            <span id="mobileCheckoutTotal" class="text-xl font-bold text-emerald-600">{{ number_format($total, 2) }} DH</span>
        </div>
        <button type="submit" 
                form="checkoutForm"
                class="flex-1 bg-gradient-to-r from-emerald-600 to-green-600 text-white font-bold py-3 px-4 rounded-xl hover:from-emerald-700 hover:to-green-700 transition-all shadow-lg flex items-center justify-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ __('checkout.confirm') }}</span>
        </button>
    </div>
</div>

<script>
    const checkoutPricing = {
        subtotal: @json((float) $subtotal),
        configuredDeliveryFee: @json((float) $deliveryFee),
        thresholdDeliveryIsFree: @json((bool) ($threshold && $subtotal > $threshold)),
        freeLabel: @json(__('checkout.free')),
        currency: 'DH',
    };

    function updateCheckoutPricing() {
        const laayouneDelivery = document.getElementById('is_laayoune_delivery');
        const deliveryFeeValue = document.getElementById('deliveryFeeValue');
        const checkoutTotal = document.getElementById('checkoutTotal');
        const mobileCheckoutTotal = document.getElementById('mobileCheckoutTotal');
        const thresholdNotice = document.getElementById('freeDeliveryThresholdNotice');
        if (!laayouneDelivery || !deliveryFeeValue || !checkoutTotal || !mobileCheckoutTotal) return;

        const deliveryIsFree = checkoutPricing.thresholdDeliveryIsFree || laayouneDelivery.checked;
        const deliveryFee = deliveryIsFree ? 0 : checkoutPricing.configuredDeliveryFee;
        const formattedTotal = `${(checkoutPricing.subtotal + deliveryFee).toFixed(2)} ${checkoutPricing.currency}`;

        deliveryFeeValue.textContent = deliveryIsFree
            ? checkoutPricing.freeLabel
            : `${checkoutPricing.configuredDeliveryFee.toFixed(2)} ${checkoutPricing.currency}`;
        deliveryFeeValue.classList.toggle('text-emerald-600', deliveryIsFree);
        deliveryFeeValue.classList.toggle('text-gray-900', !deliveryIsFree);
        checkoutTotal.textContent = formattedTotal;
        mobileCheckoutTotal.textContent = formattedTotal;

        if (thresholdNotice) {
            thresholdNotice.classList.toggle('hidden', laayouneDelivery.checked);
        }
    }

    document.getElementById('is_laayoune_delivery')?.addEventListener('change', updateCheckoutPricing);
    document.addEventListener('DOMContentLoaded', updateCheckoutPricing);

    // Format phone number as user types (Moroccan format)
    const phoneInput = document.getElementById('customer_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // Limit to 10 digits (Moroccan numbers)
            if (value.length > 10) value = value.slice(0, 10);
            
            // Format as XX XX XX XX XX
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 2 === 0) formatted += ' ';
                formatted += value[i];
            }
            e.target.value = formatted;
        });
    }

    // Update mobile sticky button visibility
    function updateMobileCheckoutSubmit() {
        const submitBar = document.getElementById('mobileCheckoutSubmit');
        if (!submitBar || window.innerWidth >= 768) return;

        const shouldShow = window.scrollY > 250;
        if (shouldShow) {
            submitBar.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
            submitBar.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
        } else {
            submitBar.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
            submitBar.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
        }
    }

    window.addEventListener('scroll', updateMobileCheckoutSubmit, { passive: true });
    window.addEventListener('resize', updateMobileCheckoutSubmit);
    document.addEventListener('DOMContentLoaded', updateMobileCheckoutSubmit);

    // Form validation and submission handling
    const form = document.getElementById('checkoutForm');
    let isSubmitting = false;

    if (form) {
        form.addEventListener('submit', function(e) {
            // Client-side validation before submission
            const name = document.getElementById('customer_name');
            const phone = document.getElementById('customer_phone');
            const city = document.getElementById('customer_city');
            const address = document.getElementById('customer_address');
            
            let isValid = true;
            
            // Reset previous errors
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            document.querySelectorAll('.border-rose-500').forEach(el => {
                el.classList.remove('border-rose-500', 'bg-rose-50');
            });
            
            // Validation
            if (!name.value.trim()) {
                showFieldError(name, @json(__('checkout.validation_name')));
                isValid = false;
            }
            
            const phoneClean = phone.value.replace(/\s/g, '');
            if (!phone.value.trim() || phoneClean.length < 9 || phoneClean.length > 10) {
                showFieldError(phone, @json(__('checkout.validation_phone')));
                isValid = false;
            }
            
            if (!city.value.trim()) {
                showFieldError(city, @json(__('checkout.validation_city')));
                isValid = false;
            }
            
            if (!address.value.trim()) {
                showFieldError(address, @json(__('checkout.validation_address')));
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = document.querySelector('.border-rose-500');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            
            // Prevent double submission
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            isSubmitting = true;
            
            // Show loading states
            const loadingOverlay = document.getElementById('loadingOverlay');
            const submitButton = document.getElementById('submitButton');
            const buttonText = document.getElementById('buttonText');
            const buttonSpinner = document.getElementById('buttonSpinner');
            
            if (loadingOverlay) loadingOverlay.classList.remove('hidden');
            if (submitButton) {
                submitButton.disabled = true;
                if (buttonText) buttonText.classList.add('hidden');
                if (buttonSpinner) buttonSpinner.classList.remove('hidden');
            }
            
            // Also disable mobile button
            const mobileButton = document.querySelector('#mobileCheckoutSubmit button');
            if (mobileButton) mobileButton.disabled = true;
            
            return true;
        });
    }
    
    // Helper functions for validation UI
    function showFieldError(field, message) {
        field.classList.add('border-rose-500', 'bg-rose-50');
        
        // Remove existing error message for this field
        const existingError = field.parentElement.querySelector('.error-message');
        if (existingError) existingError.remove();
        
        // Add new error message
        const error = document.createElement('p');
        error.className = 'error-message mt-1 text-sm text-rose-600';
        error.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${message}`;
        field.parentElement.appendChild(error);
    }
    
    // Clear errors on input
    document.querySelectorAll('#customer_name, #customer_phone, #customer_city, #customer_address').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('border-rose-500', 'bg-rose-50');
            const existingError = this.parentElement.querySelector('.error-message');
            if (existingError) existingError.remove();
        });
    });
</script>

<style>
    /* Custom scrollbar for order summary */
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Loading overlay animation */
    #loadingOverlay {
        transition: all 0.3s ease;
    }
    
    /* Smooth transitions */
    .transition-all {
        transition-duration: 200ms;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        body {
            padding-bottom: 85px;
        }
    }
    
    /* Input focus styles */
    input:focus, textarea:focus {
        outline: none;
    }
    
    /* Disabled button state */
    button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }
    
    /* Clean input styling */
    input, textarea {
        transition: all 0.2s ease;
    }
    
    input:hover, textarea:hover {
        border-color: #cbd5e1;
    }
    
    /* Radio button animation */
    .peer:checked + label {
        border-color: #10b981;
    }
</style>
@endsection
