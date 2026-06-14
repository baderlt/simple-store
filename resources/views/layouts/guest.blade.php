<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ settings('store_name', config('app.name', 'Simple Store')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * {
                font-family: {{ app()->getLocale() === 'ar' ? "'Tajawal', sans-serif" : "'Poppins', sans-serif" }};
            }
            
            :root {
                --primary-color: {{ settings('primary_color', '#B7791F') }};
                --secondary-color: {{ settings('secondary_color', '#3D2B1F') }};
                --accent-color: {{ settings('accent_color', '#F4B400') }};
                --background-color: {{ settings('background_color', '#FFFCF5') }};
            }

            .gradient-bg {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            }

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
                background-color: var(--primary-color) !important;
            }

            .border-green-500,
            .border-green-600,
            .focus\:ring-green-500:focus,
            .focus\:border-green-500:focus {
                border-color: var(--primary-color) !important;
                --tw-ring-color: var(--primary-color) !important;
            }
            
            .hover-lift {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            
            .hover-lift:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
            
            /* Custom focus styles */
            input:focus, button:focus {
                outline: none;
                ring: 2px;
                ring-color: #22c55e;
            }
            
            /* Smooth transitions */
            .transition-all {
                transition-property: all;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 300ms;
            }
            
            /* Page animations */
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.5s ease-out;
            }
            
            .animate-slide-up {
                animation: slideUp 0.5s ease-out 0.2s both;
            }

            .auth-shell {
                min-height: min(760px, calc(100vh - 3rem));
                display: grid;
                grid-template-columns: minmax(0, 0.92fr) minmax(420px, 1.08fr);
                overflow: hidden;
                border: 1px solid rgba(61, 43, 31, 0.08);
                border-radius: 2rem;
                background: #fff;
                box-shadow: 0 30px 80px rgba(61, 43, 31, 0.16);
            }

            .auth-story {
                position: relative;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                padding: clamp(2rem, 4vw, 3.5rem);
                color: #fff;
                background:
                    linear-gradient(155deg, color-mix(in srgb, var(--secondary-color) 96%, black) 0%, var(--secondary-color) 54%, color-mix(in srgb, var(--primary-color) 82%, var(--secondary-color)) 100%);
            }

            .auth-story::after {
                content: "";
                position: absolute;
                inset: 0;
                opacity: .14;
                background-image: radial-gradient(circle at 2px 2px, #fff 1px, transparent 0);
                background-size: 28px 28px;
                mask-image: linear-gradient(to bottom, #000, transparent 72%);
            }

            .auth-story__glow {
                position: absolute;
                border-radius: 999px;
                filter: blur(1px);
                pointer-events: none;
            }

            .auth-story__glow--one {
                width: 24rem;
                height: 24rem;
                top: -12rem;
                right: -12rem;
                background: color-mix(in srgb, var(--accent-color) 28%, transparent);
            }

            .auth-story__glow--two {
                width: 18rem;
                height: 18rem;
                bottom: -8rem;
                left: -8rem;
                background: rgba(255, 255, 255, .08);
            }

            .auth-brand, .auth-story__content, .auth-store-link {
                position: relative;
                z-index: 1;
            }

            .auth-brand {
                display: inline-flex;
                align-items: center;
                gap: .9rem;
                width: fit-content;
                color: #fff;
            }

            .auth-brand__logo {
                width: 3.25rem;
                height: 3.25rem;
                display: grid;
                flex: 0 0 auto;
                place-items: center;
                overflow: hidden;
                border: 1px solid rgba(255, 255, 255, .28);
                border-radius: 1rem;
                background: rgba(255, 255, 255, .14);
                box-shadow: inset 0 1px rgba(255,255,255,.2);
                font-size: 1.25rem;
            }

            .auth-brand__logo--image { background: #fff; }
            .auth-brand__logo img { width: 100%; height: 100%; object-fit: contain; }
            .auth-brand strong, .auth-brand small { display: block; }
            .auth-brand strong { font-size: 1.05rem; line-height: 1.3; }
            .auth-brand small { max-width: 15rem; margin-top: .15rem; color: rgba(255,255,255,.68); font-size: .7rem; }

            .auth-story__content {
                margin: auto 0;
                padding: 3rem 0;
            }

            .auth-eyebrow {
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                padding: .55rem .8rem;
                border: 1px solid rgba(255,255,255,.16);
                border-radius: 999px;
                background: rgba(255,255,255,.09);
                color: color-mix(in srgb, var(--accent-color) 58%, white);
                font-size: .72rem;
                font-weight: 700;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .auth-story h1 {
                max-width: 27rem;
                margin-top: 1.4rem;
                font-size: clamp(2.35rem, 4vw, 4rem);
                font-weight: 700;
                letter-spacing: -.045em;
                line-height: 1.03;
            }

            .auth-story__content > p {
                max-width: 28rem;
                margin-top: 1.1rem;
                color: rgba(255,255,255,.72);
                font-size: .94rem;
                line-height: 1.75;
            }

            .auth-benefits { margin-top: 2.2rem; display: grid; gap: 1rem; }
            .auth-benefits li { display: flex; align-items: center; gap: .9rem; }
            .auth-benefits li > span {
                width: 2.5rem;
                height: 2.5rem;
                display: grid;
                flex: 0 0 auto;
                place-items: center;
                border: 1px solid rgba(255,255,255,.16);
                border-radius: .8rem;
                background: rgba(255,255,255,.09);
                color: color-mix(in srgb, var(--accent-color) 65%, white);
            }
            .auth-benefits strong, .auth-benefits small { display: block; }
            .auth-benefits strong { font-size: .83rem; }
            .auth-benefits small { margin-top: .15rem; color: rgba(255,255,255,.58); font-size: .71rem; }
            .auth-store-link { display: inline-flex; align-items: center; gap: .6rem; width: fit-content; color: rgba(255,255,255,.72); font-size: .76rem; font-weight: 600; }
            .auth-store-link:hover { color: #fff; }

            .auth-form-panel {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: clamp(2rem, 5vw, 5rem);
                background:
                    radial-gradient(circle at 100% 0%, color-mix(in srgb, var(--primary-color) 9%, transparent), transparent 36%),
                    #fff;
            }

            .auth-mobile-brand { display: none; }
            .auth-form-wrap { width: 100%; max-width: 27rem; }
            .auth-heading { margin-bottom: 2rem; }
            .auth-heading__icon {
                width: 2.7rem;
                height: 2.7rem;
                display: grid;
                place-items: center;
                margin-bottom: 1.15rem;
                border-radius: .9rem;
                color: var(--primary-color);
                background: color-mix(in srgb, var(--primary-color) 11%, white);
            }
            .auth-heading h2 { color: #292524; font-size: clamp(1.75rem, 3vw, 2.25rem); font-weight: 700; letter-spacing: -.035em; }
            .auth-heading p { margin-top: .55rem; color: #78716c; font-size: .83rem; line-height: 1.65; }
            .auth-status { margin-bottom: 1.25rem; padding: .85rem 1rem; border: 1px solid #bbf7d0; border-radius: .8rem; background: #f0fdf4; color: #15803d; font-size: .8rem; }
            .auth-form { display: grid; gap: 1.2rem; }
            .auth-fields-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .85rem; }
            .auth-field label { display: block; color: #44403c; font-size: .76rem; font-weight: 600; }
            .auth-label-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: .5rem; }
            .auth-label-row a { color: var(--primary-color); font-size: .71rem; font-weight: 600; }
            .auth-label-row a:hover { text-decoration: underline; }
            .auth-field > label { margin-bottom: .5rem; }
            .auth-input-wrap {
                position: relative;
                display: flex;
                align-items: center;
                border: 1px solid #e7e5e4;
                border-radius: .85rem;
                background: #fafaf9;
                transition: border-color .2s, box-shadow .2s, background .2s;
            }
            .auth-input-wrap:focus-within { border-color: var(--primary-color); background: #fff; box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary-color) 12%, transparent); }
            .auth-input-wrap > i { position: absolute; left: 1rem; color: #a8a29e; font-size: .8rem; }
            .auth-input-wrap input {
                width: 100%;
                border: 0;
                border-radius: inherit;
                padding: .88rem 3rem .88rem 2.7rem;
                background: transparent;
                color: #292524;
                font-size: .82rem;
                box-shadow: none !important;
            }
            .auth-input-wrap input::placeholder { color: #a8a29e; }
            .auth-input-wrap input:focus { outline: 0; border: 0; --tw-ring-shadow: 0 0 #0000; }
            .auth-input-wrap--error { border-color: #fca5a5; background: #fff7f7; }
            .auth-password-toggle { position: absolute; right: .9rem; width: 2rem; height: 2rem; color: #a8a29e; border-radius: .5rem; }
            .auth-password-toggle:hover { color: var(--primary-color); background: color-mix(in srgb, var(--primary-color) 8%, white); }
            .auth-error { display: flex; align-items: center; gap: .35rem; margin-top: .45rem; color: #dc2626; font-size: .7rem; }
            .auth-password-hint { display: flex; align-items: center; gap: .4rem; margin-top: -.65rem; color: #a8a29e; font-size: .65rem; }
            .auth-password-hint i { color: var(--primary-color); }
            .auth-remember { display: flex; align-items: center; gap: .55rem; width: fit-content; color: #57534e; cursor: pointer; font-size: .74rem; }
            .auth-remember input { position: absolute; opacity: 0; pointer-events: none; }
            .auth-remember span { width: 1.1rem; height: 1.1rem; display: grid; place-items: center; border: 1px solid #d6d3d1; border-radius: .3rem; background: #fff; color: transparent; font-size: .55rem; transition: .2s; }
            .auth-remember input:checked + span { border-color: var(--primary-color); background: var(--primary-color); color: #fff; }
            .auth-remember input:focus-visible + span { box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 16%, transparent); }
            .auth-submit {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                min-height: 3.15rem;
                margin-top: .2rem;
                padding: .8rem 1.1rem  .8rem 1.35rem;
                border-radius: .9rem;
                color: #fff;
                background: linear-gradient(135deg, var(--primary-color), color-mix(in srgb, var(--primary-color) 64%, var(--secondary-color)));
                box-shadow: 0 12px 28px color-mix(in srgb, var(--primary-color) 25%, transparent);
                font-size: .8rem;
                font-weight: 700;
            }
            .auth-submit i { transition: transform .2s; }
            .auth-submit:hover { transform: translateY(-1px); box-shadow: 0 16px 32px color-mix(in srgb, var(--primary-color) 30%, transparent); }
            .auth-submit:hover i { transform: translateX(.2rem); }
            .auth-divider { display: flex; align-items: center; gap: .8rem; margin: 1.55rem 0 1.1rem; color: #a8a29e; font-size: .68rem; white-space: nowrap; }
            .auth-divider::before, .auth-divider::after { content: ""; width: 100%; height: 1px; background: #eeeae7; }
            .auth-register { display: flex; align-items: center; justify-content: center; gap: .55rem; min-height: 3rem; border: 1px solid #e7e5e4; border-radius: .85rem; color: #44403c; background: #fff; font-size: .76rem; font-weight: 600; }
            .auth-register i { color: var(--primary-color); }
            .auth-register:hover { border-color: color-mix(in srgb, var(--primary-color) 42%, white); color: var(--primary-color); background: color-mix(in srgb, var(--primary-color) 4%, white); }
            .auth-terms { display: flex; align-items: flex-start; gap: .6rem; color: #78716c; cursor: pointer; font-size: .68rem; line-height: 1.55; }
            .auth-terms input { position: absolute; opacity: 0; pointer-events: none; }
            .auth-terms__check { width: 1.1rem; height: 1.1rem; display: grid; flex: 0 0 auto; place-items: center; margin-top: .05rem; border: 1px solid #d6d3d1; border-radius: .3rem; background: #fff; color: transparent; font-size: .55rem; transition: .2s; }
            .auth-terms input:checked + .auth-terms__check { border-color: var(--primary-color); background: var(--primary-color); color: #fff; }
            .auth-terms input:focus-visible + .auth-terms__check { box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 16%, transparent); }
            .auth-terms strong { color: var(--primary-color); font-weight: 600; }
            .auth-security { display: flex; align-items: flex-start; justify-content: center; gap: .5rem; margin-top: 1.35rem; color: #a8a29e; font-size: .64rem; line-height: 1.5; text-align: center; }
            .auth-security i { margin-top: .15rem; color: #65a30d; }
            .auth-security strong { color: #78716c; }

            [dir="rtl"] .auth-input-wrap > i { left: auto; right: 1rem; }
            [dir="rtl"] .auth-input-wrap input { padding-right: 2.7rem; padding-left: 3rem; }
            [dir="rtl"] .auth-password-toggle { right: auto; left: .9rem; }
            [dir="rtl"] .auth-store-link i, [dir="rtl"] .auth-submit i { transform: scaleX(-1); }
            [dir="rtl"] .auth-submit:hover i { transform: scaleX(-1) translateX(.2rem); }

            .auth-shell--register .auth-form-panel { padding-top: 2rem; padding-bottom: 2rem; }
            .auth-shell--register .auth-heading { margin-bottom: 1.35rem; }
            .auth-shell--register .auth-form { gap: .9rem; }

            @media (max-width: 900px) {
                .auth-shell { grid-template-columns: 1fr; min-height: auto; max-width: 34rem; margin: 0 auto; border-radius: 1.6rem; }
                .auth-story { display: none; }
                .auth-form-panel { display: block; padding: 2rem; }
                .auth-mobile-brand { display: block; margin-bottom: 2rem; }
                .auth-mobile-brand a { display: flex; align-items: center; justify-content: center; gap: .65rem; color: #292524; }
                .auth-mobile-brand a > span { width: 2.35rem; height: 2.35rem; display: grid; place-items: center; border-radius: .75rem; color: #fff; background: var(--primary-color); }
                .auth-mobile-brand img { max-width: 8rem; max-height: 3.5rem; object-fit: contain; }
                .auth-heading { text-align: center; }
                .auth-heading__icon { margin-left: auto; margin-right: auto; }
            }

            @media (max-width: 520px) {
                .auth-form-panel { padding: 1.5rem 1.15rem; }
                .auth-heading { margin-bottom: 1.5rem; }
                .auth-shell { border: 0; border-radius: 1.25rem; box-shadow: 0 18px 55px rgba(61, 43, 31, .11); }
                .auth-fields-grid { grid-template-columns: 1fr; }
            }

            @media (prefers-reduced-motion: reduce) {
                .animate-fade-in, .animate-slide-up { animation: none; }
                .auth-submit, .auth-submit i { transition: none; }
            }
        </style>
    </head>
    <body class="min-h-screen" style="background-color: var(--background-color)">
    

        <!-- Main Content -->
        <div class="min-h-screen flex flex-col justify-center items-center p-3 sm:p-6 animate-fade-in">
            <!-- Logo/Header -->
            <div class="w-full max-w-md px-6 {{ request()->routeIs('login', 'register') ? 'hidden' : '' }}">
                <div class="text-center mt-2">
                    @php
                        $logoPath = settings('logo');
                        $storeName = settings('store_name', 'Simple Store');
                    @endphp
                    
                    <a href="{{ route('home') }}" class="inline-flex items-center space-x-3 hover-lift">
                        @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                            <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}" 
                                 class="h-20 w-auto object-contain transition-transform duration-300 hover:scale-105">
                        @else 
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-600 text-white p-3 rounded-lg">
                                    <i class="fas fa-jar text-3xl"></i>
                                </div>
                                <div class="text-left">
                                    <h1 class="text-3xl font-bold text-gray-800">{{ $storeName }}</h1>
                                    <p class="text-gray-600 text-sm mt-1">Des produits choisis avec élégance</p>
                                </div>
                            </div>
                        @endif 
                    </a>
                </div>
            </div>

            <!-- Form Container -->
            <div class="w-full {{ request()->routeIs('login', 'register') ? 'max-w-6xl' : 'sm:max-w-md mt-4 px-6' }} animate-slide-up">
                <div class="{{ request()->routeIs('login', 'register') ? '' : 'bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100' }}">
                    <!-- Decorative top bar -->
                    @unless(request()->routeIs('login', 'register'))
                        <div class="h-2 gradient-bg"></div>
                    @endunless
                    
                    <!-- Form content -->
                    <div class="{{ request()->routeIs('login', 'register') ? '' : 'px-8 py-8' }}">
                        {{ $slot }}
                    </div>
                    
                    <!-- Bottom decorative bar -->
                    @unless(request()->routeIs('login', 'register'))
                        <div class="h-1 bg-gray-100"></div>
                    @endunless
                </div>
                
                <!-- Additional links -->
                <div class="mt-8 text-center {{ request()->routeIs('login', 'register') ? 'hidden' : '' }}">
                    <p class="text-gray-600 text-sm">
                        © {{ date('Y') }} {{ $storeName }}. Tous droits réservés.
                        <a href="{{ route('home') }}" class="text-green-600 hover:text-green-700 font-medium ml-2">
                            <i class="fas fa-home mr-1"></i> Retour à l'accueil
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Background Pattern -->
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute -top-1/2 -right-1/2 w-full h-full opacity-5">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%2322c55e" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
        </div>

        <!-- Back to Top Button (for longer pages) -->
        <button id="backToTop" class="fixed bottom-8 right-8 bg-green-600 text-white p-3 rounded-full shadow-lg hover:bg-green-700 transition-all opacity-0 transform translate-y-10 z-40">
            <i class="fas fa-chevron-up"></i>
        </button>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Back to Top Button
                const backToTopButton = document.getElementById('backToTop');
                
                if (backToTopButton) {
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
                }

                // Auto-hide messages after 5 seconds
                setTimeout(() => {
                    const messages = document.querySelectorAll('[class*="messages"]');
                    messages.forEach(message => {
                        message.style.transition = 'opacity 0.5s';
                        message.style.opacity = '0';
                        setTimeout(() => message.remove(), 500);
                    });
                }, 5000);
                
                // Add focus styles to form elements
                const formInputs = document.querySelectorAll('input, textarea, select');
                formInputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.parentElement.classList.add('ring-2', 'ring-green-500', 'ring-opacity-50');
                    });
                    
                    input.addEventListener('blur', function() {
                        this.parentElement.classList.remove('ring-2', 'ring-green-500', 'ring-opacity-50');
                    });
                });
            });
        </script>
    </body>
</html>
