@extends('layouts.app')

@section('title', settings('seo_title', settings('store_name', 'Simple Store')))
@section('description', settings('seo_description', settings('store_description', 'A flexible ecommerce storefront for any business niche.')))
@section('keywords', settings('seo_keywords', 'ecommerce, online store, products, shopping'))

@section('content')
@php
    $primary = settings('primary_color', '#2563EB');
    $accent = settings('accent_color', '#F59E0B');
    $buttonRadius = match(settings('button_style', 'rounded')) {
        'pill' => 'rounded-full',
        'square' => 'rounded-none',
        default => 'rounded-2xl',
    };
@endphp

@foreach($homepageSections as $section)
    @php($sectionSettings = $section->settings ?? [])
    @php($type = $sectionSettings['type'] ?? $section->key)

    @switch($type)
        @case('hero')
            <section class="relative overflow-hidden bg-slate-950 text-white">
                <div class="absolute inset-0 opacity-30" style="background: radial-gradient(circle at 20% 20%, {{ $primary }}, transparent 32%), radial-gradient(circle at 80% 0%, {{ $accent }}, transparent 28%);"></div>
                <div class="container mx-auto px-4 py-20 md:py-28 relative z-10 grid lg:grid-cols-2 gap-10 items-center">
                    <div>
                        <span class="inline-flex items-center gap-2 bg-white/10 border border-white/15 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                            <i class="fas fa-sparkles"></i> {{ $sectionSettings['badge'] ?? settings('hero_badge', 'Configurable ecommerce platform') }}
                        </span>
                        <h1 class="text-4xl md:text-6xl font-black leading-tight mb-6">
                            {{ $sectionSettings['title'] ?? settings('hero_title', 'Build a storefront for any niche') }}
                        </h1>
                        <p class="text-xl text-white/80 mb-8 max-w-2xl">
                            {{ $sectionSettings['subtitle'] ?? settings('hero_subtitle', 'Manage branding, products, homepage sections, content, shipping, taxes, and localization from one reusable Laravel commerce back office.') }}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ $sectionSettings['primary_url'] ?? settings('hero_primary_button_url', route('products.index')) }}" class="{{ $buttonRadius }} inline-flex items-center justify-center gap-2 bg-white text-slate-950 px-7 py-4 font-bold hover:scale-105 transition-transform">
                                {{ $sectionSettings['primary_text'] ?? settings('hero_primary_button_text', 'Shop products') }} <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="{{ $sectionSettings['secondary_url'] ?? settings('hero_secondary_button_url', route('categories.index')) }}" class="{{ $buttonRadius }} inline-flex items-center justify-center gap-2 border border-white/30 px-7 py-4 font-bold hover:bg-white/10 transition-colors">
                                {{ $sectionSettings['secondary_text'] ?? settings('hero_secondary_button_text', 'Browse categories') }}
                            </a>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($stats as $label => $value)
                            <div class="rounded-3xl bg-white/10 border border-white/15 p-6 backdrop-blur">
                                <div class="text-3xl font-black">{{ number_format($value) }}+</div>
                                <div class="text-white/70 capitalize">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('slider')
            @php($position = $sectionSettings['banner_position'] ?? 'hero')
            @if(isset($activeBanners[$position]) && count($activeBanners[$position]) > 0)
                <section class="relative overflow-hidden">
                    <div class="swiper hero-swiper">
                        <div class="swiper-wrapper">
                            @foreach($activeBanners[$position] as $banner)
                                <div class="swiper-slide">
                                    <div class="relative h-[420px] md:h-[560px]">
                                        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}" loading="lazy" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/20"></div>
                                        <div class="absolute inset-0 flex items-center">
                                            <div class="container mx-auto px-4">
                                                <div class="max-w-2xl text-white">
                                                    @if($banner->title)<h2 class="text-4xl md:text-6xl font-black mb-4">{{ $banner->title }}</h2>@endif
                                                    @if($banner->description)<p class="text-xl text-white/85 mb-8">{{ $banner->description }}</p>@endif
                                                    @if($banner->cta_text && $banner->cta_link)<a href="{{ $banner->cta_link }}" class="{{ $buttonRadius }} inline-flex bg-white text-slate-950 px-7 py-4 font-bold">{{ $banner->cta_text }}</a>@endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </section>
            @endif
            @break

        @case('categories')
            <section id="categories" class="py-16 bg-white">
                <div class="container mx-auto px-4">
                    <div class="text-center max-w-2xl mx-auto mb-10">
                        <p class="text-sm uppercase tracking-wide font-bold" style="color: {{ $primary }}">{{ $sectionSettings['eyebrow'] ?? 'Shop by category' }}</p>
                        <h2 class="text-3xl md:text-4xl font-black text-gray-900">{{ $sectionSettings['title'] ?? 'Explore collections' }}</h2>
                        <p class="text-gray-500 mt-3">{{ $sectionSettings['description'] ?? 'Create categories for any catalog structure, from apparel sizes to electronics departments or restaurant menus.' }}</p>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach($categories->take($sectionSettings['limit'] ?? 8) as $category)
                            <a href="{{ route('categories.show', $category) }}" class="group rounded-3xl border border-gray-100 bg-gray-50 p-6 hover:shadow-xl transition-all">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white mb-5" style="background: {{ $primary }}"><i class="fas fa-layer-group"></i></div>
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600">{{ $category->name }}</h3>
                                <p class="text-gray-500 mt-2 line-clamp-2">{{ $category->description ?: __('messages.products') }}</p>
                                <p class="text-sm font-semibold mt-4">{{ $category->products_count }} {{ __('messages.products') }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('featured_products')
            <section class="py-16 bg-gray-50">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
                        <div>
                            <p class="text-sm uppercase tracking-wide font-bold" style="color: {{ $primary }}">{{ $sectionSettings['eyebrow'] ?? 'Featured' }}</p>
                            <h2 class="text-3xl md:text-4xl font-black text-gray-900">{{ $sectionSettings['title'] ?? 'Selected products' }}</h2>
                            <p class="text-gray-500 mt-2">{{ $sectionSettings['description'] ?? 'Highlight products dynamically from your admin catalog.' }}</p>
                        </div>
                        <a href="{{ route('products.index') }}" class="font-bold hover:underline" style="color: {{ $primary }}">View all products</a>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($featuredProducts->take($sectionSettings['limit'] ?? 8) as $product)
                            <article class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-shadow">
                                <a href="{{ route('products.show', $product->slug) }}" class="block aspect-square bg-gray-100">
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-box-open text-5xl"></i></div>
                                    @endif
                                </a>
                                <div class="p-5">
                                    <p class="text-xs uppercase tracking-wide text-gray-400">{{ $product->category?->name }}</p>
                                    <h3 class="font-bold text-gray-900 mt-1 line-clamp-2"><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h3>
                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="text-lg font-black" style="color: {{ $primary }}">{{ number_format($product->final_price, 2) }} {{ settings('currency', 'USD') }}</span>
                                        <form method="POST" action="{{ route('cart.add', $product->id) }}">@csrf<button class="w-10 h-10 rounded-full text-white" style="background: {{ $primary }}"><i class="fas fa-cart-plus"></i></button></form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('promotional_cards')
            @php($position = $sectionSettings['banner_position'] ?? 'middle')
            @if(isset($activeBanners[$position]) && count($activeBanners[$position]) > 0)
                <section class="py-16 bg-white"><div class="container mx-auto px-4 grid md:grid-cols-2 gap-6">
                    @foreach($activeBanners[$position] as $banner)
                        <a href="{{ $banner->cta_link ?: '#' }}" class="relative min-h-[260px] rounded-3xl overflow-hidden group">
                            <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/45"></div>
                            <div class="relative z-10 p-8 text-white max-w-md"><h3 class="text-3xl font-black">{{ $banner->title }}</h3><p class="mt-3 text-white/85">{{ $banner->description }}</p></div>
                        </a>
                    @endforeach
                </div></section>
            @endif
            @break

        @case('testimonials')
            @php($items = $sectionSettings['items'] ?? [])
            @if(count($items))
                <section class="py-16 bg-gray-50"><div class="container mx-auto px-4"><h2 class="text-3xl font-black text-center mb-10">{{ $sectionSettings['title'] ?? 'What customers say' }}</h2><div class="grid md:grid-cols-3 gap-6">@foreach($items as $item)<blockquote class="bg-white rounded-3xl p-6 border border-gray-100"><p class="text-gray-600">“{{ $item['quote'] ?? '' }}”</p><footer class="mt-4 font-bold">{{ $item['name'] ?? 'Customer' }}</footer></blockquote>@endforeach</div></div></section>
            @endif
            @break

        @case('brands')
            @php($items = $sectionSettings['items'] ?? [])
            @if(count($items))
                <section class="py-12 bg-white"><div class="container mx-auto px-4"><h2 class="sr-only">Brands</h2><div class="grid grid-cols-2 md:grid-cols-6 gap-4">@foreach($items as $item)<div class="rounded-2xl border border-gray-100 p-5 text-center font-bold text-gray-500">{{ $item['name'] ?? $item }}</div>@endforeach</div></div></section>
            @endif
            @break

        @case('newsletter')
            <section class="py-16 bg-slate-950 text-white"><div class="container mx-auto px-4 text-center max-w-3xl"><h2 class="text-3xl md:text-4xl font-black">{{ $sectionSettings['title'] ?? settings('newsletter_title', 'Stay in the loop') }}</h2><p class="text-white/70 mt-3">{{ $sectionSettings['description'] ?? settings('newsletter_description', 'Get launches, offers, and updates from our store.') }}</p><form class="mt-8 flex flex-col sm:flex-row gap-3"><input type="email" placeholder="you@example.com" class="flex-1 rounded-2xl border-white/10 bg-white text-slate-900 px-5 py-4"><button class="{{ $buttonRadius }} px-7 py-4 font-bold" style="background: {{ $primary }}">Subscribe</button></form></div></section>
            @break

        @case('custom_html')
            <section class="py-12"><div class="container mx-auto px-4">{!! $sectionSettings['html'] ?? '' !!}</div></section>
            @break

        @case('contact_map')
            <section class="py-16 bg-white"><div class="container mx-auto px-4 grid lg:grid-cols-2 gap-8 items-start"><div><p class="text-sm uppercase tracking-wide font-bold" style="color: {{ $primary }}">Contact</p><h2 class="text-3xl font-black text-gray-900">{{ settings('store_name', 'Simple Store') }}</h2><div class="mt-6 space-y-3 text-gray-600">@if(settings('address'))<p><i class="fas fa-location-dot mr-2"></i>{{ settings('address') }}</p>@endif @if(settings('phone'))<p><i class="fas fa-phone mr-2"></i>{{ settings('phone') }}</p>@endif @if(settings('email'))<p><i class="fas fa-envelope mr-2"></i>{{ settings('email') }}</p>@endif @if(settings('working_hours'))<p><i class="fas fa-clock mr-2"></i>{{ settings('working_hours') }}</p>@endif</div></div>@if(settings('maps_link'))<iframe src="{{ settings('maps_link') }}" class="w-full h-80 rounded-3xl border-0" loading="lazy"></iframe>@else<div class="w-full h-80 rounded-3xl bg-gray-100 flex items-center justify-center text-gray-400">Add a map link in admin settings</div>@endif</div></section>
            @break
    @endswitch
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.Swiper && document.querySelector('.hero-swiper')) {
        new Swiper('.hero-swiper', { loop: true, pagination: { el: '.swiper-pagination', clickable: true }, autoplay: { delay: 5000 } });
    }
});
</script>
@endpush
