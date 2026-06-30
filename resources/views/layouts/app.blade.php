<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $pageTitle = trim($__env->yieldContent('title', settings('seo_title', settings('store_name', 'Simple Store'))));
        $pageDescription = trim($__env->yieldContent('description', settings('seo_description', settings('store_description', 'Discover carefully selected products from our store.'))));
        $pageCanonical = trim($__env->yieldContent('canonical', url()->current()));
        $pageOgTitle = trim($__env->yieldContent('og_title', $pageTitle));
        $pageOgDescription = trim($__env->yieldContent('og_description', $pageDescription));
        $pageTwitterTitle = trim($__env->yieldContent('twitter_title', $pageOgTitle));
        $pageTwitterDescription = trim($__env->yieldContent('twitter_description', $pageOgDescription));
        $pageOgImage = trim($__env->yieldContent('og_image', ''));
        $pageOgImageAlt = trim($__env->yieldContent('og_image_alt', settings('store_name', 'Simple Store')));
    @endphp
    
    {{-- SEO Meta Tags --}}
    <title>{{ $pageTitle }} - {{ settings('store_slogan', 'Premium products, beautifully presented') }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="@yield('keywords', settings('seo_keywords', 'online store, premium products, delivery'))">
    <meta name="author" content="{{ settings('store_name', 'Simple Store') }}">
    
    {{-- Canonical --}}
    <link rel="canonical" href="{{ $pageCanonical }}">
    
    {{-- Open Graph --}}
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_MA' : 'fr_MA' }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $pageOgTitle }}">
    <meta property="og:description" content="{{ $pageOgDescription }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:site_name" content="{{ settings('store_name', 'Simple Store') }}">
    @php
        $ogImage = $pageOgImage !== ''
            ? $pageOgImage
            : ($ogImage ?? (settings('logo') ? asset('storage/' . settings('logo')) : asset('img/default-og.jpg')));
    @endphp
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:secure_url" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $pageOgImageAlt }}">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTwitterTitle }}">
    <meta name="twitter:description" content="{{ $pageTwitterDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="twitter:image:alt" content="{{ $pageOgImageAlt }}">
    
    {{-- Robots --}}
    <meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
    
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Performance Optimizations --}}
    @include('layouts.google-fonts')
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Styles --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.typography-overrides')
    
    {{-- Favicon --}}
    @php
        $logoPath = settings('logo');
        $faviconPath = settings('favicon', $logoPath);
    @endphp
    @if($faviconPath && file_exists(public_path('storage/'.$faviconPath)))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $faviconPath) }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/' . $faviconPath) }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/' . $faviconPath) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $faviconPath) }}">
        <meta name="msapplication-TileImage" content="{{ asset('storage/' . $faviconPath) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    @endif
        
    <style>
        :root {
            --primary-color: {{ settings('primary_color', '#B7791F') }};
            --secondary-color: {{ settings('secondary_color', '#3D2B1F') }};
            --accent-color: {{ settings('accent_color', '#F4B400') }};
            --background-color: {{ settings('background_color', '#FFFCF5') }};
            --button-color: {{ settings('button_color', settings('primary_color', '#B7791F')) }};
        }

        
        /* Dynamic brand color overrides for common Tailwind green utility classes */
        .text-green-400,
        .text-green-500,
        .text-emerald-500,
        .text-teal-500,
        .text-green-600,
        .text-emerald-600,
        .text-teal-600,
        .text-green-700,
        .hover\:text-green-600:hover,
        .hover\:text-green-700:hover,
        .hover\:text-emerald-600:hover,
        .hover\:text-teal-600:hover {
            color: var(--primary-color) !important;
        }

        .bg-green-500,
        .bg-emerald-500,
        .bg-teal-500,
        .bg-green-600,
        .bg-green-700,
        .bg-emerald-600,
        .bg-emerald-700,
        .bg-teal-600,
        .bg-teal-700,
        .hover\:bg-green-500:hover,
        .hover\:bg-green-600:hover,
        .hover\:bg-green-700:hover,
        .hover\:bg-emerald-600:hover,
        .hover\:bg-emerald-700:hover,
        .hover\:bg-teal-600:hover,
        .hover\:bg-teal-700:hover {
            background-color: var(--button-color) !important;
        }

        .bg-green-50,
        .bg-green-100,
        .hover\:bg-green-50:hover,
        .hover\:bg-green-100:hover {
            background-color: color-mix(in srgb, var(--primary-color) 12%, white) !important;
        }

        .border-green-500,
        .border-green-600,
        .focus\:ring-green-500:focus,
        .focus\:border-green-500:focus {
            border-color: var(--primary-color) !important;
            --tw-ring-color: var(--primary-color) !important;
        }

        .from-green-400,
        .from-green-500,
        .from-green-600,
        .from-emerald-400,
        .from-emerald-500,
        .from-emerald-600 {
            --tw-gradient-from: var(--primary-color) var(--tw-gradient-from-position) !important;
            --tw-gradient-to: color-mix(in srgb, var(--primary-color) 0%, transparent) var(--tw-gradient-to-position) !important;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to) !important;
        }

        .to-green-500,
        .to-green-600,
        .to-green-700,
        .to-emerald-500,
        .to-emerald-600,
        .to-emerald-700 {
            --tw-gradient-to: var(--secondary-color) var(--tw-gradient-to-position) !important;
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        /* Dynamic brand color overrides for common Tailwind green utility classes */
        /* .text-green-600,.text-green-700,.text-green-400,.hover\:text-green-600:hover,.hover\:text-green-700:hover{color:var(--primary-color)!important;}
        .bg-green-600,.bg-green-100,.hover\:bg-green-600:hover,.hover\:bg-green-50:hover{background-color:color-mix(in srgb,var(--primary-color) 16%, white)!important;}
        .border-green-600,.border-green-500,.focus\:ring-green-500:focus{border-color:var(--primary-color)!important;}
.gradient-bg {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
} */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }

        /*
         * Product names may contain Arabic, Latin text, numbers and
         * punctuation in the same sentence. Let each value determine its own
         * direction instead of inheriting RTL/LTR from the whole page.
         */
        .bidi-auto {
            unicode-bidi: plaintext;
            text-align: start;
        }

        .bidi-auto-block {
            display: block;
            width: 100%;
        }

        [dir="rtl"] .bidi-auto,
        [dir="rtl"] .bidi-auto .bidi-text,
        [dir="rtl"] .bidi-auto-block {
            text-align: right !important;
        }

        /*
         * Stable desktop navigation structure.
         * These rules are intentionally defined here instead of relying on
         * generated Tailwind gap utilities, so they work immediately after
         * deployment in both LTR and RTL layouts.
         */
        @media (min-width: 1024px) {
            .desktop-nav {
                column-gap: 1rem;
            }

            .desktop-nav-item {
                display: inline-flex !important;
                flex: 0 0 9.75rem;
                width: 9.75rem;
                min-height: 2.75rem;
                align-items: center;
                justify-content: center;
                column-gap: 0.625rem;
                padding-inline: 0.75rem;
                white-space: nowrap;
                text-align: center;
            }

            .desktop-nav-item > i,
            .desktop-nav-item-icon {
                flex: 0 0 auto;
                margin: 0 !important;
            }

            .desktop-nav-item-label {
                display: inline-block;
                min-width: 0;
            }
        }
        
        /* Add these styles for cart drawer */
        .transform {
            transition-property: transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }
        
        .fa-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes ping {
            75%, 100% {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        .animate-ping {
            animation: ping 0.5s cubic-bezier(0, 0, 0.2, 1);
        }
        
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        [data-action]:hover {
            transform: scale(1.1);
            transition: transform 0.2s;
        }
        
        /* Mobile header fixes */
        @media (max-width: 1023px) {
            .header-container {
                position: relative;
                flex-wrap: wrap;
                min-height: 4.5rem;
                padding-top: 0.375rem;
                padding-bottom: 0.375rem;
                direction: ltr;
            }
            .logo-container {
                order: 1;
                position: absolute;
                top: 50%;
                left: 50%;
                right: auto;
                width: min(9rem, calc(100% - 9.5rem));
                max-width: none;
                transform: translate(-50%, -50%);
                z-index: 1;
            }
            .logo-container > a {
                justify-content: center;
            }
            .header-logo {
                width: auto;
                max-width: 100%;
                height: 3.5rem;
            }
            .mobile-buttons {
                order: 2;
                width: 100%;
                justify-content: flex-end;
                direction: ltr;
                gap: 0.375rem;
            }
            #mobileMenuButton {
                position: absolute;
                left: 0;
                right: auto;
                top: 50%;
                transform: translateY(-50%);
                z-index: 2;
                padding: 0.5rem;
            }
            #mobile-search-toggle,
            #cart-drawer-trigger {
                position: relative;
                z-index: 2;
            }
            #cart-count {
                right: -0.25rem;
                left: auto;
            }
            .search-container-mobile {
                order: 3;
                width: 100%;
                margin-top: 0.5rem;
                direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            }
            .desktop-nav {
                display: none !important;
            }
            .mobile-search-visible {
                display: block !important;
            }

            .mobile-menu-panel {
                left: 0;
                width: 70vw !important;
                min-width: 0 !important;
                max-width: 70vw !important;
                flex-basis: 70vw;
                border-radius: 0 1.5rem 1.5rem 0;
                transform: translateX(-105%);
                transition: transform 320ms cubic-bezier(0.22, 1, 0.36, 1);
            }

            .mobile-menu-panel.is-open {
                transform: translateX(0);
            }

            .mobile-menu-backdrop {
                opacity: 0;
                visibility: hidden;
                transition: opacity 250ms ease, visibility 250ms ease;
            }

            .mobile-menu-backdrop.is-open {
                opacity: 1;
                visibility: visible;
            }
        }

        @media (max-width: 374px) {
            .header-logo {
                height: 3.125rem;
            }
            .logo-container {
                width: min(7.5rem, calc(100% - 8.75rem));
            }
            .mobile-buttons {
                gap: 0.125rem;
            }
        }
        
        @media (min-width: 1024px) {
            .search-container-mobile {
                display: none !important;
            }
        }
        
        /* Search suggestions dropdown */
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-top: 0.25rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .search-suggestion-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .search-suggestion-item:hover {
            background-color: #f9fafb;
        }
        
        .search-suggestion-item:last-child {
            border-bottom: none;
        }
        
        .search-suggestion-name {
            font-weight: 500;
            color: #374151;
        }
        
        .search-suggestion-category {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        
        .search-suggestion-price {
            font-weight: 600;
            color: var(--primary-color);
            margin-top: 0.25rem;
        }
        
        .search-suggestion-highlight {
            background-color: #dcfce7;
            padding: 0 2px;
            border-radius: 2px;
        }
        
        .search-loading {
            padding: 1rem;
            text-align: center;
            color: #6b7280;
        }
        
        /* Search button animations */
        .search-btn:hover {
            transform: scale(1.05);
            transition: transform 0.2s;
        }
        
        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }
        
        /* Improve mobile touch targets */
        @media (max-width: 640px) {
            button, a {
                min-height: 20px;
                min-width: 20px;
            }
            
            input, select, textarea {
                font-size: 16px; /* Prevents iOS zoom on focus */
            }
        }
    </style>
    @stack('head')
    @yield('styles')
</head>
<body style="background-color: var(--background-color)" class="bg-gray-50 min-h-screen flex flex-col">
     {{-- Top Bar --}}
   {{--  <div class="gradient-bg text-white text-sm py-2">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <span><i class="fas fa-phone-alt mr-2"></i> {{ settings('phone', '+212 XXX-XXXXXX') }}</span>
                    <span><i class="fas fa-envelope mr-2"></i> {{ settings('email', 'contact@maisondoree.ma') }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="hidden md:inline">Bonjour, {{ Auth::user()->name }}</span>
                    @endauth
                    <div class="flex space-x-2">
                        <a href="{{ settings('facebook_url', '#') }}" target="_blank" class="hover:text-gray-200">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="{{ settings('instagram_url', '#') }}" target="_blank" class="hover:text-gray-200">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Header --}}
    <header class="bg-white shadow-sm sticky top-0 z-40 border-b">
        <div class="container mx-auto px-4">
            {{-- First Row: Logo + Action Buttons --}}
            <div class="header-container flex items-center justify-between pt-2 lg:pt-4">
                @php
                    $logoPath = settings('logo');
                    $storeName = settings('store_name', 'Simple Store');
                @endphp
                
                {{-- Logo --}}
                <div class="logo-container">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 hover-lift">
                        @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                            <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}" 
                            loading="lazy"
                                 class="header-logo h-11 sm:h-12 lg:h-16 w-auto max-w-full object-contain transition-transform duration-300 hover:scale-105">
                                     {{-- <span class="text-lg font-bold text-gray-800 lg:hidden">{{ $storeName }}</span> --}}
                        @else 
                            <div class="flex items-center space-x-2">
                                <div class="bg-green-600 text-white p-1 lg:p-2 rounded-lg">
                                    <i class="fas fa-jar text-lg lg:text-2xl"></i>
                                </div>
                                <span class="text-lg lg:text-2xl font-bold text-gray-800 hidden sm:block">{{ $storeName }}</span>
                            </div>
                        @endif 
                    </a>
                </div>
                
                {{-- Desktop Search Bar --}}
                <div class="hidden lg:flex flex-1 mx-8 max-w-2xl relative">
                    <form action="{{ route('products.index') }}" method="GET" class="w-full" id="desktop-search-form">
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   id="desktop-search-input"
                                   placeholder="{{ __('messages.search_products') }}" 
                                   class="w-full px-4 py-2 pl-12 pr-10 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                            <button type="submit"
                                    aria-label="{{ app()->getLocale() === 'ar' ? 'بحث' : 'Rechercher' }}"
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-green-600 hover:text-green-700 transition-colors">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                    {{-- Desktop Search Suggestions --}}
                    <div id="desktop-search-suggestions" class="search-suggestions hidden"></div>
                </div>

                {{-- Action Buttons --}}
                <div class="mobile-buttons flex items-center space-x-3 lg:space-x-6">
                    {{-- Mobile Search Button (Icon only) --}}
                    <button type="button" id="mobile-search-toggle"
                            aria-label="{{ app()->getLocale() === 'ar' ? 'فتح البحث' : 'Ouvrir la recherche' }}"
                            aria-controls="mobile-search-container"
                            aria-expanded="false"
                            class="lg:hidden text-gray-600 hover:text-green-600 p-2 rounded-full hover:bg-gray-100 transition-colors">
                        <i class="fas fa-search text-xl"></i>
                    </button>
                    
                    <!-- Pack Icon -->
                    <button type="button" id="cart-drawer-trigger" class="relative group focus:outline-none"
                            aria-label="{{ __('cart.drawer_title') }}"
                            aria-controls="cart-drawer"
                            aria-expanded="false">
                        <div class="bg-green-50 p-2 rounded-full group-hover:bg-green-100 transition-colors">
                            <i class="fas fa-box-open text-lg lg:text-xl text-green-600"></i>
                            <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold shadow-sm">
                                @if(session('cart'))
                                    {{ array_sum(array_column(session('cart'), 'quantity')) }}
                                @else
                                    0
                                @endif
                            </span>
                        </div>
                    </button>

                    {{-- Language Switcher (desktop/tablet) --}}
                    <div class="hidden sm:flex items-center rounded-full border border-gray-200 bg-gray-50 p-1 text-xs font-semibold">
                        <a href="{{ route('lang.switch', 'fr') }}"
                           class="px-2.5 py-1 rounded-full transition-colors {{ app()->getLocale() === 'fr' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:text-green-600' }}">
                            FR
                        </a>
                        <a href="{{ route('lang.switch', 'ar') }}"
                           class="px-2.5 py-1 rounded-full transition-colors {{ app()->getLocale() === 'ar' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:text-green-600' }}">
                            AR
                        </a>
                    </div>
                    
                    @auth
                        <div x-data="{ open: false }" 
                             x-on:click.outside="open = false"
                             class="relative hidden lg:block">
                            <button x-on:click="open = !open" 
                                    class="flex items-center space-x-2 text-gray-700 hover:text-green-600 focus:outline-none">
                                <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center">
                                    <i class="fas fa-user"></i>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200 hidden lg:block" 
                                   :class="{'rotate-180': open}"></i>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-100">
                                
                                @if(Auth::user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors duration-200 group border-b border-gray-100">
                                        <div class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-tachometer-alt text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ __('messages.admin_dashboard') }}</div>
                                            <div class="text-xs text-gray-500">{{ __('messages.site_management') }}</div>
                                        </div>
                                    </a>
                                @endif
                                
                                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-3 text-left text-red-600 hover:bg-red-50 transition-colors duration-200 group">
                                        <div class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-sign-out-alt text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ __('messages.logout') }}</div>
                                            <div class="text-xs text-gray-500">{{ __('messages.logout') }}</div>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="hidden lg:flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-green-600 hover:text-green-700 font-medium">
                                <i class="fas fa-sign-in-alt mr-2"></i>{{ __('messages.login') }}
                            </a>
                            <a href="{{ route('register') }}" class="gradient-bg text-white px-4 py-2 rounded-lg hover:shadow-lg transition-shadow font-medium">
                                <i class="fas fa-user-plus mr-2"></i>{{ __('messages.register') }}
                            </a>
                        </div>
                    @endauth
                    
                    {{-- Mobile Menu Button --}}
                    <button id="mobileMenuButton"
                            type="button"
                            aria-controls="mobileMenu"
                            aria-expanded="false"
                            aria-label="{{ __('messages.menu') }}"
                            class="lg:hidden text-gray-700">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>

            {{-- Mobile Search Bar (Always visible when toggled) --}}
            <div id="mobile-search-container" class="search-container-mobile hidden relative">
                <form action="{{ route('products.index') }}" method="GET" class="w-full" id="mobile-search-form">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               id="mobile-search-input"
                               placeholder="{{ __('messages.search_products') }}" 
                               class="w-full px-4 py-2 pl-12 pr-10 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                               value="{{ request('search') }}"
                               autocomplete="off">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <button type="submit"
                                aria-label="{{ app()->getLocale() === 'ar' ? 'بحث' : 'Rechercher' }}"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-green-600 hover:text-green-700 transition-colors">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
                {{-- Mobile Search Suggestions --}}
                <div id="mobile-search-suggestions" class="search-suggestions hidden"></div>
            </div>

            {{-- Desktop Navigation --}}
            <nav class="desktop-nav -mt-1 hidden lg:flex items-center justify-center">
                <a href="{{ route('home') }}" class="desktop-nav-item nav-link text-gray-700 hover:text-green-600 font-medium">
                    <i class="fas fa-home"></i>
                    <span class="desktop-nav-item-label">{{ __('messages.home') }}</span>
                </a>
                <a href="{{ route('products.index') }}" class="desktop-nav-item nav-link text-gray-700 hover:text-green-600 font-medium">
                    <i class="fas fa-basket-shopping"></i>
                    <span class="desktop-nav-item-label">{{ __('messages.products') }}</span>
                </a>
                <a href="{{ route('categories.index') }}" class="desktop-nav-item nav-link text-gray-700 hover:text-green-600 font-medium">
                    <i class="fas fa-th-large"></i>
                    <span class="desktop-nav-item-label">{{ __('messages.categories') }}</span>
                </a>
                <a href="{{ route('promotions.index') }}" 
                   class="desktop-nav-item nav-link text-red-600 font-bold hover:text-red-700
                          rounded-lg bg-red-50 hover:bg-red-100 transition-all duration-200">
                    <div class="desktop-nav-item-icon relative">
                        <i class="fas fa-tags"></i>
                        <div class="absolute -inset-2 bg-red-200 rounded-full opacity-0 
                                    group-hover:opacity-30 transition-opacity duration-300"></div>
                    </div>
                    <span class="desktop-nav-item-label">{{ __('messages.promotions') }}</span>
                    <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                </a>
                @auth
                    <a href="{{ route('orders.index') }}" class="desktop-nav-item nav-link text-gray-700 hover:text-green-600 font-medium">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="desktop-nav-item-label">{{ __('messages.my_orders') }}</span>
                    </a>
                @endauth
            </nav>

            {{-- Mobile Menu --}}
            <div id="mobileMenuBackdrop"
                 class="mobile-menu-backdrop fixed inset-0 z-40 bg-gray-950/45 backdrop-blur-[2px] lg:hidden"
                 aria-hidden="true"></div>

            <aside id="mobileMenu"
                   class="mobile-menu-panel fixed inset-y-0 z-50 flex flex-col overflow-hidden bg-white shadow-2xl lg:hidden"
                   style="width: 70vw; min-width: 0; max-width: 70vw;"
                   aria-hidden="true"
                   aria-label="{{ __('messages.menu') }}">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <div class="flex min-w-0 items-center gap-3">
                        @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                            <img src="{{ asset('storage/'.$logoPath) }}"
                                 alt="{{ $storeName }}"
                                 class="h-10 w-10 rounded-xl object-contain">
                        @else
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600">
                                <i class="fas fa-store"></i>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate font-bold text-gray-900">{{ $storeName }}</p>
                            <p class="text-xs text-gray-500">{{ __('messages.menu') }}</p>
                        </div>
                    </div>
                    <button id="mobileMenuClose"
                            type="button"
                            aria-label="{{ __('messages.close') }}"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-600 transition hover:bg-gray-200 hover:text-gray-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-4 py-5">
                    <div class="flex flex-col space-y-3">
                    <a href="{{ route('home') }}" class="flex items-center text-gray-700 hover:text-green-600 p-2 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-home mr-3"></i>{{ __('messages.home') }}
                    </a>
                    <a href="{{ route('products.index') }}" class="flex items-center text-gray-700 hover:text-green-600 p-2 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-basket-shopping mr-3"></i>{{ __('messages.products') }}
                    </a>
                    <a href="{{ route('categories.index') }}" class="flex items-center text-gray-700 hover:text-green-600 p-2 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-th-large mr-3"></i>{{ __('messages.categories') }}
                    </a>
                    <a href="{{ route('promotions.index') }}" 
                       class="flex items-center text-red-600 font-bold hover:text-red-700 p-2 rounded-lg bg-red-50 hover:bg-red-100 transition-all duration-200">
                        <i class="fas fa-tags mr-3"></i>
                        <span>{{ __('messages.promotions') }}</span>
                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse ml-1"></div>
                    </a>

                    {{-- Language Switcher (mobile menu) --}}
                    <div class="border-t pt-3">
                        <div class="flex items-center justify-between gap-3 p-2 rounded-lg bg-gray-50">
                            <span class="text-sm font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-globe mr-2 text-green-600"></i>{{ __('messages.language') }}
                            </span>
                            <div class="flex rounded-full border border-gray-200 bg-white p-1 text-xs font-semibold">
                                <a href="{{ route('lang.switch', 'fr') }}"
                                   class="px-3 py-1 rounded-full transition-colors {{ app()->getLocale() === 'fr' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:text-green-600' }}">
                                    FR
                                </a>
                                <a href="{{ route('lang.switch', 'ar') }}"
                                   class="px-3 py-1 rounded-full transition-colors {{ app()->getLocale() === 'ar' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:text-green-600' }}">
                                    AR
                                </a>
                            </div>
                        </div>
                    </div>
                    @auth
                        <a href="{{ route('orders.index') }}" class="flex items-center text-gray-700 hover:text-green-600 p-2 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-clipboard-list mr-3"></i>{{ __('messages.my_orders') }}
                        </a>
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center text-gray-700 hover:text-green-600 p-2 rounded-lg hover:bg-gray-50">
                                <i class="fas fa-tachometer-alt mr-3"></i>{{ __('messages.admin_dashboard') }}
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="mt-2 pt-2 border-t">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center w-full text-left text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                <span>{{ __('messages.logout') }}</span>
                            </button>
                        </form>
                    @else
                        <div class="flex flex-col gap-3 border-t pt-3">
                            <a href="{{ route('login') }}" class="w-full text-center px-4 py-2.5 text-green-600 hover:text-green-700 font-medium border border-green-600 rounded-lg">
                                {{ __('messages.login') }}
                            </a>
                            <a href="{{ route('register') }}" class="w-full text-center gradient-bg text-white px-4 py-2.5 rounded-lg hover:shadow-lg transition-shadow font-medium">
                                {{ __('messages.register') }}
                            </a>
                        </div>
                    @endauth
                    </div>
                </div>
            </aside>
        </div>
    </header>

    {{-- Content --}}
    <main class="flex-grow">
        
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white ">
        <div class="container mx-auto px-4 py-12">
            <div class="grid md:grid-cols-4 gap-8">
                {{-- Company Info --}}
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                            <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}" 
                                 class="h-12 w-auto object-contain"
                                 loading="lazy">
                        @else
                            <div class="bg-green-600 text-white p-2 rounded-lg">
                                <i class="fas fa-jar text-2xl"></i>
                            </div>
                        @endif
                        <span class="text-2xl font-bold">{{ $storeName }}</span>
                    </div>
                    <p class="text-gray-300">{{ settings('footer_text', settings('store_slogan', 'Premium products for every lifestyle.')) }}</p>
                    <div class="flex space-x-4 pt-2">
                        <a href="{{ settings('facebook_url', '#') }}" target="_blank" 
                           class="bg-gray-800 hover:bg-green-600 w-10 h-10 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="{{ settings('instagram_url', '#') }}" target="_blank" 
                           class="bg-gray-800 hover:bg-green-600 w-10 h-10 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @foreach(['twitter_url' => 'fa-x-twitter', 'tiktok_url' => 'fa-tiktok', 'youtube_url' => 'fa-youtube'] as $socialKey => $icon)
                            @if(settings($socialKey))
                                <a href="{{ settings($socialKey) }}" target="_blank" rel="noopener" class="bg-gray-800 hover:bg-green-600 w-10 h-10 rounded-full flex items-center justify-center transition-colors"><i class="fab {{ $icon }}"></i></a>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="text-xl font-bold mb-6 text-green-400">{{ __('messages.navigation') }}</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-green-400 transition-colors flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2"></i>{{ __('messages.home') }}
                        </a></li>
                        <li><a href="{{ route('products.index') }}" class="text-gray-300 hover:text-green-400 transition-colors flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2"></i>{{ __('messages.products') }}
                        </a></li>
                        <li><a href="{{ route('categories.index') }}" class="text-gray-300 hover:text-green-400 transition-colors flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2"></i>{{ __('messages.categories') }}
                        </a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="text-xl font-bold mb-6 text-green-400">{{ __('messages.contact') }}</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt shrink-0 text-green-400 mt-1"></i>
                            <span class="text-gray-300">{{ settings('address', 'Adresse par défaut') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone shrink-0 text-green-400"></i>
                            <span class="text-gray-300">{{ settings('phone', '+212 XXX-XXXXXX') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope shrink-0 text-green-400"></i>
                            <span class="text-gray-300">{{ settings('email', 'contact@maisondoree.ma') }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Hours & Payment --}}
                <div>
                    <h3 class="text-xl font-bold mb-6 text-green-400">{{ __('messages.hours') }}</h3>
                    @php
                        $footerWorkingHours = working_hours_parts(
                            (string) settings('working_hours', 'Lun-Sam: 9h-20h')
                        );
                    @endphp
                    <div class="space-y-2">
                        @forelse($footerWorkingHours as $period)
                            <div class="flex items-center justify-between gap-4 rounded-lg border border-gray-700 bg-gray-800 px-3 py-2.5"
                                 dir="auto">
                                <span class="min-w-0 font-medium text-gray-300">
                                    {{ $period['days'] }}{{ $period['hours'] !== '' ? ':' : '' }}
                                </span>
                                @if($period['hours'] !== '')
                                    <span class="whitespace-nowrap font-bold text-white" dir="ltr">
                                        {{ $period['hours'] }}
                                    </span>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-300">{{ settings('working_hours', 'Lun-Sam: 9h-20h') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400">
                    &copy; {{ date('Y') }} {{ $storeName }}. {{ __('messages.rights') }} 
                    <span class="mx-2">|</span>
                    <a href="#" class="hover:text-green-400">Politique de confidentialité</a>
                    <span class="mx-2">|</span>
                    <a href="#" class="hover:text-green-400">Conditions générales</a>
                </p>
            </div>
        </div>
    </footer>

    {{-- Back to Top Button --}}
    <button id="backToTop" type="button"
            aria-label="{{ app()->getLocale() === 'ar' ? 'العودة إلى الأعلى' : 'Retour en haut' }}"
            class="fixed bottom-8 right-8 bg-green-600 text-white p-3 rounded-full shadow-lg hover:bg-green-700 transition-all opacity-0 transform translate-y-10 z-40">
        <i class="fas fa-chevron-up"></i>
    </button>

    {{-- Pack Drawer --}}
    <div id="cart-drawer" class="fixed inset-0 z-50 overflow-hidden hidden"
         role="dialog"
         aria-modal="true"
         aria-hidden="true"
         aria-labelledby="cart-drawer-title">
        <!-- Backdrop -->
        <div id="cart-drawer-backdrop" class="absolute inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <!-- Drawer Panel -->
        <div class="absolute inset-y-0 right-0 flex max-w-full">
            <div class="relative w-screen max-w-md">
                <!-- Panel Content -->
                <div class="h-full flex flex-col bg-white shadow-xl transform transition-transform duration-300 ease-in-out translate-x-full">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 id="cart-drawer-title" class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-box-open mr-2 text-emerald-600"></i>
                            {{ __('cart.drawer_title') }}
                        </h2>
                        <button type="button" id="close-cart-drawer"
                                aria-label="{{ __('messages.close') }}"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Pack Content -->
                    <div class="flex-1 overflow-y-auto">
                        <div id="cart-drawer-content" class="px-6 py-4" style="display: block">
                            <!-- Loading State -->
                            <div id="cart-loading" class="hidden py-8">
                                <div class="flex justify-center">
                                    <i class="fas fa-spinner fa-spin text-3xl text-emerald-600"></i>
                                </div>
                                <p class="text-center text-gray-500 mt-4">{{ __('cart.loading') }}</p>
                            </div>
                            
                            <!-- Empty Pack -->
                            <div id="cart-empty" class="hidden py-8 text-center">
                                <div class="text-gray-400 mb-4">
                                    <i class="fas fa-box-open text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('cart.empty_title') }}</h3>
                                <p class="text-gray-500 mb-6">{{ __('cart.empty_description') }}</p>
                                <button type="button" id="close-cart-empty" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                                    <i class="fas fa-store mr-2"></i>
                                    {{ __('cart.view_products') }}
                                </button>
                            </div>
                            
                            <!-- Pack Items -->
                            <div id="cart-items" class="space-y-4">
                                <!-- Items will be loaded here via AJAX -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="border-t border-gray-200 px-6 py-4">
                        <!-- Summary -->
                        <div id="cart-summary" class="mb-4 hidden">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">{{ __('cart.subtotal') }}</span>
                                <span id="cart-subtotal" class="font-semibold">0.00 DH</span>
                            </div>
                            <div class="flex justify-between mb-4">
                                <span class="text-gray-600">{{ __('cart.total') }}</span>
                                <span id="cart-total" class="text-xl font-bold text-emerald-600">0.00 DH</span>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex space-x-3">
                            <a href="{{ route('cart.index') }}" class="flex-1 text-center bg-gray-100 text-gray-700 hover:bg-gray-200 py-3 rounded-lg font-medium transition-colors">
                                <i class="fas fa-shopping-bag mr-2"></i>
                                {{ __('messages.view_cart') }}
                            </a>
                            <a href="{{ route('checkout.index') }}" id="checkout-btn" class="flex-1 text-center bg-emerald-600 text-white hover:bg-emerald-700 py-3 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-lock mr-2"></i>
                                {{ __('order_now') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $appTranslations = [
            'cart_empty_line' => __('cart.empty_line'),
            'adding' => __('cart.adding'),
            'added' => __('cart.added_short'),
            'error' => __('cart.error'),
            'product.max_quantity_units' => __('product.max_quantity_units'),
            'product.share_discover' => __('product.share_discover'),
            'product.share_success' => __('product.share_success'),
            'product.share_error' => __('product.share_error'),
            'product.link_copied' => __('product.link_copied'),
            'product.copy_error' => __('product.copy_error'),
            'product.copy_link' => __('product.copy_link'),
            'product.only_stock_available' => __('product.only_stock_available'),
            'product.invalid_quantity' => __('product.invalid_quantity'),
            'product.minimum_quantity_required' => __('product.minimum_quantity_required'),
            'product.redirecting_checkout' => __('product.redirecting_checkout'),
            'products.none_found' => __('products.none_found'),
            'products.sku' => __('products.sku'),
            'products.category' => __('products.category'),
            'validation.required_field' => __('validation.required_field'),
            'discounts.end_date_after_start' => __('discounts.end_date_after_start'),
            'discounts.select_product' => __('discounts.select_product'),
            'discounts.enter_discount_value' => __('discounts.enter_discount_value'),
        ];
    @endphp
    <script>
        window.appTranslations = {!! json_encode($appTranslations, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search elements
        const mobileSearchContainer = document.getElementById('mobile-search-container');
        const mobileSearchToggle = document.getElementById('mobile-search-toggle');
        const mobileSearchInput = document.getElementById('mobile-search-input');
        const desktopSearchInput = document.getElementById('desktop-search-input');
        const mobileSearchSuggestions = document.getElementById('mobile-search-suggestions');
        const desktopSearchSuggestions = document.getElementById('desktop-search-suggestions');
        
        // Debounce function for search
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (character) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[character];
            });
        }

        function escapeRegExp(value) {
            return String(value ?? '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        
        // Function to highlight search term in text
        function highlightText(text, searchTerm) {
            const safeText = escapeHtml(text);

            if (!searchTerm) return safeText;

            const safeSearchTerm = escapeHtml(searchTerm);
            const regex = new RegExp(`(${escapeRegExp(safeSearchTerm)})`, 'gi');

            return safeText.replace(regex, '<span class="search-suggestion-highlight">$1</span>');
        }
        
        // Function to fetch search suggestions
        async function fetchSearchSuggestions(searchTerm, searchType) {
            if (searchTerm.length < 2) {
                return [];
            }
            
            try {
                const response = await fetch(`{{ route('products.search.suggestions') }}?q=${encodeURIComponent(searchTerm)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                if (response.ok) {
                    return await response.json();
                }
                return [];
            } catch (error) {
                console.error('Error fetching suggestions:', error);
                return [];
            }
        }
        
        // Function to display search suggestions
        function displaySearchSuggestions(suggestions, searchTerm, searchType) {
            const suggestionsContainer = searchType === 'mobile' ? mobileSearchSuggestions : desktopSearchSuggestions;
            
            if (suggestions.length === 0) {
                suggestionsContainer.innerHTML = `
                    <div class="search-suggestion-item">
                        <div class="text-gray-500 text-center">Aucun produit trouvé</div>
                    </div>
                `;
                suggestionsContainer.classList.remove('hidden');
                return;
            }
            const baseProductRoute = '{{ route("products.show", ":slug") }}';
            let html = '';
            suggestions.forEach(product => {
                const highlightedName = highlightText(product.name, searchTerm);
                const price = product.has_discount ? product.final_price : product.price;
                const productUrl = baseProductRoute.replace(':slug', encodeURIComponent(product.slug ?? ''));
                
                html += `
              <a href="${escapeHtml(productUrl)}"
                    class="search-suggestion-item block">
                        <div class="search-suggestion-name bidi-auto" dir="auto">${highlightedName}</div>
                        ${product.category_name ? `
                            <span class="search-suggestion-category">
                                <i class="fas fa-tag text-xs mr-1"></i>${escapeHtml(product.category_name)}
                            </span>
                        ` : ''}
                        <span class="ml-4 search-suggestion-price">${escapeHtml(price)} DH</span>
                    </a>
                `;
            });
            
            // Add "View all results" link
            html += `
                <a href="{{ route('products.index') }}?search=${encodeURIComponent(searchTerm)}" 
                   class="search-suggestion-item block text-center font-medium text-green-600 hover:text-green-700 border-t">
                    <i class="fas fa-search mr-2"></i>
                    Voir tous les résultats
                </a>
            `;
            
            suggestionsContainer.innerHTML = html;
            suggestionsContainer.classList.remove('hidden');
        }
        
        // Function to handle search input
        const handleSearchInput = debounce(async function(event, searchType) {
            const searchTerm = event.target.value.trim();
            const suggestionsContainer = searchType === 'mobile' ? mobileSearchSuggestions : desktopSearchSuggestions;
            
            if (searchTerm.length < 2) {
                suggestionsContainer.classList.add('hidden');
                return;
            }
            
            // Show loading
            suggestionsContainer.innerHTML = `
                <div class="search-loading">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Recherche...
                </div>
            `;
            suggestionsContainer.classList.remove('hidden');
            
            // Fetch suggestions
            const suggestions = await fetchSearchSuggestions(searchTerm, searchType);
            displaySearchSuggestions(suggestions, searchTerm, searchType);
        }, 300);
        
        // Event listeners for search inputs
        if (mobileSearchInput) {
            mobileSearchInput.addEventListener('input', (e) => handleSearchInput(e, 'mobile'));
            mobileSearchInput.addEventListener('focus', (e) => {
                if (mobileSearchInput.value.trim().length >= 2) {
                    handleSearchInput(e, 'mobile');
                }
            });
        }
        
        if (desktopSearchInput) {
            desktopSearchInput.addEventListener('input', (e) => handleSearchInput(e, 'desktop'));
            desktopSearchInput.addEventListener('focus', (e) => {
                if (desktopSearchInput.value.trim().length >= 2) {
                    handleSearchInput(e, 'desktop');
                }
            });
        }
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.search-container-mobile') && !event.target.closest('#mobile-search-toggle')) {
                mobileSearchSuggestions.classList.add('hidden');
            }
            if (!event.target.closest('.desktop-nav') && !event.target.closest('#desktop-search-input')) {
                desktopSearchSuggestions.classList.add('hidden');
            }
        });
        
        // Hide suggestions on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                mobileSearchSuggestions.classList.add('hidden');
                desktopSearchSuggestions.classList.add('hidden');
            }
        });
        
        // Toggle mobile search
        if (mobileSearchToggle) {
            mobileSearchToggle.addEventListener('click', function() {
                mobileSearchContainer.classList.toggle('hidden');
                const isSearchOpen = !mobileSearchContainer.classList.contains('hidden');
                mobileSearchToggle.setAttribute('aria-expanded', isSearchOpen ? 'true' : 'false');
                if (isSearchOpen) {
                    // Focus on input when search is shown
                    setTimeout(() => {
                        if (mobileSearchInput) {
                            mobileSearchInput.focus();
                            // Show suggestions if there's text
                            if (mobileSearchInput.value.trim().length >= 2) {
                                handleSearchInput({ target: mobileSearchInput }, 'mobile');
                            }
                        }
                    }, 100);
                } else {
                    mobileSearchSuggestions.classList.add('hidden');
                }
            });
        }
        
        // Cart drawer elements
        const cartDrawer = document.getElementById('cart-drawer');
        const cartDrawerTrigger = document.getElementById('cart-drawer-trigger');
        const closeCartDrawer = document.getElementById('close-cart-drawer');
        const cartDrawerBackdrop = document.getElementById('cart-drawer-backdrop');
        const closeCartEmptyBtn = document.getElementById('close-cart-empty');
        
        // Cart drawer functions
        function openCartDrawer() {
            cartDrawer.classList.remove('hidden');
            cartDrawer.setAttribute('aria-hidden', 'false');
            cartDrawerTrigger?.setAttribute('aria-expanded', 'true');
            setTimeout(() => {
                cartDrawer.querySelector('.transform').classList.remove('translate-x-full');
                loadCartContent();
            }, 10);
            document.body.style.overflow = 'hidden';
        }
        
        function closeCartDrawerFunc() {
            cartDrawer.querySelector('.transform').classList.add('translate-x-full');
            setTimeout(() => {
                cartDrawer.classList.add('hidden');
                cartDrawer.setAttribute('aria-hidden', 'true');
                cartDrawerTrigger?.setAttribute('aria-expanded', 'false');
            }, 300);
            document.body.style.overflow = '';
        }
        
        // Event listeners for drawer
        if (cartDrawerTrigger) {
            cartDrawerTrigger.addEventListener('click', openCartDrawer);
        }
        
        if (closeCartDrawer) {
            closeCartDrawer.addEventListener('click', closeCartDrawerFunc);
        }
        
        if (cartDrawerBackdrop) {
            cartDrawerBackdrop.addEventListener('click', closeCartDrawerFunc);
        }
        
        if (closeCartEmptyBtn) {
            closeCartEmptyBtn.addEventListener('click', closeCartDrawerFunc);
        }
        
        // Close drawer with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !cartDrawer.classList.contains('hidden')) {
                closeCartDrawerFunc();
            }
        });
        
        // Function to load cart content
        async function loadCartContent() {
            try {
                const loading = document.getElementById('cart-loading');
                const empty = document.getElementById('cart-empty');
                const items = document.getElementById('cart-items');
                const summary = document.getElementById('cart-summary');
                const checkoutBtn = document.getElementById('checkout-btn');
                
                // Show loading
                loading.classList.remove('hidden');
                empty.classList.add('hidden');
                items.classList.add('hidden');
                summary.classList.add('hidden');
                checkoutBtn.classList.add('disabled');
                
                const response = await fetch('{{ route("cart.ajax.get") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    if (data.items && data.items.length > 0) {
                        // Show items
                        renderCartItems(data.items);
                        updateCartSummary(data);
                        
                        empty.classList.add('hidden');
                        items.classList.remove('hidden');
                        summary.classList.remove('hidden');
                        checkoutBtn.classList.remove('disabled');
                    } else {
                        // Show empty cart
                        empty.classList.remove('hidden');
                        items.classList.add('hidden');
                        summary.classList.add('hidden');
                        checkoutBtn.classList.add('disabled');
                    }
                } else {
                    throw new Error('Failed to load cart');
                }
            } catch (error) {
                console.error('Error loading cart:', error);
            } finally {
                document.getElementById('cart-loading').classList.add('hidden');
            }
        }
        
        // Function to render cart items
        function renderCartItems(items) {
            const itemsContainer = document.getElementById('cart-items');
            itemsContainer.innerHTML = '';
            
            if (!items || items.length === 0) {
                itemsContainer.innerHTML = `<p class="text-center text-gray-500 py-4">${escapeHtml(window.appTranslations.cart_empty_line)}</p>`;
                return;
            }
            
            const baseRoute = '{{ route("products.show", ":slug") }}';
            
            items.forEach(item => {
                const unitPrice = item.has_discount ? Number(item.final_price) : Number(item.price);
                const itemTotal = unitPrice * Number(item.quantity);
                
                const itemId = escapeHtml(item.key || item.id);
                const productUrl = escapeHtml(baseRoute.replace(':slug', encodeURIComponent(item.slug ?? '')));
                const itemName = escapeHtml(item.name);
                const itemDisplayName = escapeHtml(item.display_name || item.name);
                const itemVariantLabel = escapeHtml(item.variant_label || '');
                const itemImage = escapeHtml(item.image || '');
                
                const itemElement = `
                    <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg"
                         data-item-id="${itemId}"
                         data-unit-price="${unitPrice}"
                         data-minimum-quantity="${Number(item.minimum_quantity || 1)}">
                        <!-- Product Image -->
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                ${item.image ? 
                                    `<img src="/storage/${itemImage}" alt="${itemName}" loading="lazy" class="w-full h-full object-cover">` :
                                    `<div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>`
                                }
                            </div>
                        </div>
                        
                        <!-- Product Info -->
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 text-sm line-clamp-1">
                                <a href="${productUrl}" class="bidi-auto bidi-auto-block hover:text-emerald-600" dir="auto">
                                    ${itemDisplayName}
                                </a>
                            </h4>
                            
                            <!-- Price -->
                            <div class="bidi-auto text-xs text-gray-500 mt-1" dir="auto">${itemVariantLabel}</div>
                            <div class="flex items-center justify-between mt-2">
                                <div>
                                    ${item.has_discount ? 
                                        `<div class="text-sm">
                                            <span class="font-bold text-emerald-600">${item.final_price.toFixed(2)} DH</span>
                                            <span class="text-gray-400 line-through text-xs ml-2">${item.price.toFixed(2)} DH</span>
                                            <div class="text-xs text-rose-600 mt-0.5">{{ __('cart.total') }} <span class="cart-line-total inline-block" dir="ltr" style="unicode-bidi: isolate;">${itemTotal.toFixed(2)} DH</span></div>
                                        </div>` :
                                        `<div>
                                            <span class="font-bold text-gray-900">${item.price.toFixed(2)} DH</span>
                                            <div class="text-xs text-gray-600 mt-0.5">{{ __('cart.total') }} <span class="cart-line-total inline-block" dir="ltr" style="unicode-bidi: isolate;">${itemTotal.toFixed(2)} DH</span></div>
                                        </div>`
                                    }
                                </div>
                                
                                <!-- Quantity Controls -->
                                <div class="flex items-center space-x-2">
                                    <button type="button"
                                            data-action="decrease"
                                            data-product-id="${itemId}"
                                            aria-label="{{ app()->getLocale() === 'ar' ? 'إنقاص الكمية' : 'Diminuer la quantité' }}"
                                            class="h-8 w-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 transition-colors">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    
                                    <span class="quantity-display min-w-8 text-center font-medium">${item.quantity}</span>
                                    
                                    <button type="button"
                                            data-action="increase"
                                            data-product-id="${itemId}"
                                            aria-label="{{ app()->getLocale() === 'ar' ? 'زيادة الكمية' : 'Augmenter la quantité' }}"
                                            class="h-8 w-8 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 transition-colors">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Remove Button -->
                        <button type="button"
                                data-product-id="${itemId}"
                                aria-label="{{ app()->getLocale() === 'ar' ? 'حذف المنتج من الباقة' : 'Retirer le produit du pack' }}"
                                class="remove-from-cart-btn text-red-400 hover:text-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                
                itemsContainer.innerHTML += itemElement;
            });
            
            // Add event listeners to new buttons
            attachCartItemEvents();
        }
        
        // Function to update cart summary
        function updateCartSummary(data) {
            document.getElementById('cart-subtotal').textContent = data.total.toFixed(2) + ' DH';
            document.getElementById('cart-total').textContent = data.total.toFixed(2) + ' DH';
        }

        function formatDrawerMoney(value) {
            return `${Number(value || 0).toFixed(2)} DH`;
        }

        function parseDrawerMoney(value) {
            return Number(String(value || '0').replace(/[^\d.-]/g, '')) || 0;
        }

        function updateCartLineTotal(itemElement, quantity, serverLineTotal = null) {
            if (!itemElement) return 0;

            const lineTotal = serverLineTotal !== null
                ? parseDrawerMoney(serverLineTotal)
                : parseDrawerMoney(itemElement.dataset.unitPrice) * Number(quantity || 0);
            const lineTotalElement = itemElement.querySelector('.cart-line-total');

            if (lineTotalElement) {
                lineTotalElement.textContent = formatDrawerMoney(lineTotal);
            }

            return lineTotal;
        }

        function refreshCartSummaryFromRows() {
            const total = [...document.querySelectorAll('#cart-items [data-item-id]')]
                .reduce((sum, itemElement) => sum + parseDrawerMoney(itemElement.querySelector('.cart-line-total')?.textContent), 0);

            updateCartSummary({ total });
        }
        
        // Attach events to cart item buttons
        function attachCartItemEvents() {
            document.querySelectorAll('[data-action="increase"], [data-action="decrease"]').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const productId = this.getAttribute('data-product-id');
                    const action = this.getAttribute('data-action');
                    const itemElement = this.closest('[data-item-id]');
                    const quantityDisplay = itemElement.querySelector('.quantity-display');
                    const minimumQuantity = parseInt(itemElement.dataset.minimumQuantity || '1', 10) || 1;
                    let currentQuantity = parseInt(quantityDisplay.textContent);
                    
                    if (action === 'increase') {
                        currentQuantity++;
                    } else if (action === 'decrease' && currentQuantity > minimumQuantity) {
                        currentQuantity--;
                    } else {
                        return;
                    }
                    
                    this.disabled = true;
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i>';

                    quantityDisplay.textContent = currentQuantity;
                    updateCartLineTotal(itemElement, currentQuantity);
                    refreshCartSummaryFromRows();
                    
                    await updateCartItemQuantity(productId, currentQuantity);
                    
                    this.disabled = false;
                    this.innerHTML = originalHTML;
                });
            });
            
            document.querySelectorAll('.remove-from-cart-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const productId = this.getAttribute('data-product-id');
                    
                    this.disabled = true;
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    
                    await removeFromCart(productId);
                });
            });
        }
        
        // Function to update cart item quantity
        async function updateCartItemQuantity(productId, quantity) {
            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('quantity', quantity);
                formData.append('_method', 'PUT');

                const response = await fetch('{{ route("cart.ajax.update", ":id") }}'.replace(':id', productId), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    updateCartCount(data.cart_count);
                    
                    const itemElement = document.querySelector(`[data-item-id="${productId}"]`);
                    const quantityDisplay = itemElement?.querySelector('.quantity-display');
                    if (quantityDisplay) {
                        quantityDisplay.textContent = data.quantity;
                    }
                    updateCartLineTotal(itemElement, data.quantity, data.item_total);
                    
                    updateCartSummary({
                        total: parseFloat(data.cart_total.replace(',', '')) || 0
                    });
                    
                } else {
                    loadCartContent();
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
            }
        }
        
        // Function to remove item from cart
        async function removeFromCart(productId) {
            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'DELETE');

                const response = await fetch('{{ route("cart.ajax.remove", ":id") }}'.replace(':id', productId), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    updateCartCount(data.cart_count);
                    
                    const itemElement = document.querySelector(`[data-item-id="${productId}"]`);
                    if (itemElement) {
                        itemElement.remove();
                    }
                    
                    updateCartSummary({
                        total: parseFloat(data.cart_total.replace(',', '')) || 0
                    });
                    
                    if (data.items_count === 0) {
                        document.getElementById('cart-empty').classList.remove('hidden');
                        document.getElementById('cart-items').classList.add('hidden');
                        document.getElementById('cart-summary').classList.add('hidden');
                        document.getElementById('checkout-btn').classList.add('disabled');
                    }
                    
                } 
            } catch (error) {
                console.error('Error removing item:', error);
            }
        }
        
        // Function to update cart count in navigation
        function updateCartCount(count) {
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = count;
                
                cartCountElement.classList.add('animate-ping');
                setTimeout(() => {
                    cartCountElement.classList.remove('animate-ping');
                }, 500);
            }
        }
                
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-to-cart-btn')) {
                const button = e.target.closest('.add-to-cart-btn');
                e.preventDefault();
                
                if (button.disabled) return;
                
                const productId = button.getAttribute('data-product-id');
                const productName = button.getAttribute('data-product-name');
                const variantInput = document.getElementById('selectedVariantId');
                const isCurrentProduct = variantInput?.dataset.productId === String(productId);
                const variantId = button.getAttribute('data-variant-id') || (isCurrentProduct ? variantInput.value : '');
                const quantityInput = isCurrentProduct ? document.getElementById('quantity') : null;
                if (quantityInput && typeof validateProductMinimumQuantity === 'function' && !validateProductMinimumQuantity()) {
                    return;
                }

                const quantity = button.getAttribute('data-quantity') || (quantityInput ? Number(quantityInput.value || 1) : 1);
                
                addToCart(productId, productName, button, variantId, quantity);
            }
        });    
        
        // Function to handle add to cart
        async function addToCart(productId, productName, button, variantId = null, quantity = 1) {
            const originalContent = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;
            
            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                if (variantId) {
                    formData.append('variant_id', variantId);
                }
                formData.append('quantity', quantity);

                const response = await fetch('{{ route("cart.add", ":id") }}'.replace(':id', productId), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    updateCartCount(data.cart_count);
                    
                    button.classList.add('bg-green-500', 'from-green-500', 'to-green-600');
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    
                    setTimeout(() => {
                        openCartDrawer();
                    }, 500);
                    
                    setTimeout(() => {
                        button.classList.remove('bg-green-500', 'from-green-500', 'to-green-600');
                        button.innerHTML = originalContent;
                        button.disabled = false;
                    }, 1500);
                    
                } else {
                    if (data.message && typeof showQuantityWarning === 'function') {
                        showQuantityWarning(data.message);
                    }
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        }
        
        // Mobile Menu Drawer
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuClose = document.getElementById('mobileMenuClose');
        const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');
        const mobileMenuIcon = mobileMenuButton?.querySelector('i');

        function setMobileMenu(open) {
            mobileMenu.classList.toggle('is-open', open);
            mobileMenuBackdrop.classList.toggle('is-open', open);
            mobileMenu.setAttribute('aria-hidden', String(!open));
            mobileMenuBackdrop.setAttribute('aria-hidden', String(!open));
            mobileMenuButton.setAttribute('aria-expanded', String(open));
            mobileMenuIcon.classList.toggle('fa-bars', !open);
            mobileMenuIcon.classList.toggle('fa-times', open);
            document.body.classList.toggle('overflow-hidden', open);

            if (open) {
                mobileSearchSuggestions?.classList.add('hidden');
                window.setTimeout(() => mobileMenuClose.focus(), 200);
            } else {
                mobileMenuButton.focus();
            }
        }

        mobileMenuButton?.addEventListener('click', () => {
            setMobileMenu(!mobileMenu.classList.contains('is-open'));
        });

        mobileMenuClose?.addEventListener('click', () => setMobileMenu(false));
        mobileMenuBackdrop?.addEventListener('click', () => setMobileMenu(false));

        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => setMobileMenu(false));
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && mobileMenu.classList.contains('is-open')) {
                setMobileMenu(false);
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024 && mobileMenu.classList.contains('is-open')) {
                setMobileMenu(false);
            }
        });

        // Back to Top Button
        const backToTopButton = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('opacity-0', 'translate-y-10');
                backToTopButton.classList.add('opacity-100', 'translate-y-0');
            } else {
                backToTopButton.classList.remove('opacity-100', 'translate-y-0');
                backToTopButton.classList.add('opacity-0', 'translate-y-10');
            }
        });

        backToTopButton.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Auto-hide messages after 5 seconds
        setTimeout(() => {
            const messages = document.querySelectorAll('[class*="messages"]');
            messages.forEach(message => {
                message.style.transition = 'opacity 0.5s';
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 500);
            });
        }, 5000);
    });
    </script>
<style>
/* Button loading states */
button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Quantity button animations */
[data-action]:hover {
    transform: scale(1.1);
    transition: transform 0.2s;
}

/* Spinner animation */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

@yield('scripts')

</body>
</html>
