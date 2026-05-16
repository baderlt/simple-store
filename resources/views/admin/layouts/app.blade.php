<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('admin_panel') . ' - ' . __('dashboard'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Smooth transitions */
        .sidebar-link, .user-avatar {
            transition: all 0.2s ease;
        }
        
        /* Card hover effects */
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        /* Mobile menu animation */
        .sidebar-mobile {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar-mobile.active {
            transform: translateX(0);
        }
        
        /* Backdrop for mobile sidebar */
        .sidebar-backdrop {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }
        
        .sidebar-backdrop.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Responsive text sizes */
        @media (max-width: 640px) {
            .text-responsive {
                font-size: 0.875rem;
            }
            
            .text-responsive-lg {
                font-size: 1rem;
            }
        }
        
        /* Hide scrollbar on mobile for better UX */
        @media (max-width: 768px) {
            .hide-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    {{-- Mobile Header --}}
    <div class="lg:hidden bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center space-x-3">
                <button id="mobileMenuButton" class="p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-bars text-gray-700 text-lg"></i>
                </button>
                <div class="flex items-center space-x-2">
                    @php
                        $logoPath = settings('logo');
                        $storeName = settings('store_name', 'Parapharmacy');
                    @endphp
                    @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                        <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}" 
                             class="h-8 w-auto object-contain">
                    @else 
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-2 rounded-lg">
                            <i class="fas fa-prescription-bottle-medical text-white text-sm"></i>
                        </div>
                    @endif
                    <span class="font-bold text-gray-800">Admin</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                {{-- Mobile Notifications --}}
                <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full">
                    <i class="fas fa-bell"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                {{-- Mobile Language Switcher --}}
                <div class="flex rounded-full border border-gray-200 bg-white p-1 text-xs font-semibold">
                    <a href="{{ route('lang.switch', 'en') }}"
                       class="px-2 py-1 rounded-full transition-colors {{ app()->getLocale() === 'en' ? 'bg-green-600 text-white' : 'text-gray-600' }}">
                        EN
                    </a>
                    <a href="{{ route('lang.switch', 'fr') }}"
                       class="px-2 py-1 rounded-full transition-colors {{ app()->getLocale() === 'fr' ? 'bg-green-600 text-white' : 'text-gray-600' }}">
                        FR
                    </a>
                    <a href="{{ route('lang.switch', 'ar') }}"
                       class="px-2 py-1 rounded-full transition-colors {{ app()->getLocale() === 'ar' ? 'bg-green-600 text-white' : 'text-gray-600' }}">
                        AR
                    </a>
                </div>

                {{-- Mobile User Avatar --}}
                <a href="{{ route('admin.profile.edit') }}" class="user-avatar w-8 h-8 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full flex items-center justify-center shadow-md">
                    <span class="font-bold text-white text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </a>
            </div>
        </div>
    </div>
    
    <div class="flex h-screen">
        {{-- Desktop Sidebar --}}
        <aside class="hidden lg:flex w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white flex-col shadow-xl">
            {{-- Logo Section --}}
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                    @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                        <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}" 
                             class="h-12 w-auto object-contain transition-transform duration-300 hover:scale-105">
                    @else 
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-3 rounded-xl shadow-lg">
                            <i class="fas fa-prescription-bottle-medical text-white text-lg"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold">{{ __('admin_panel') }}</h1>
                        <p class="text-gray-400 text-sm">{{ __('pharmacy_management') }}</p>
                    </div>
                </div>
            </div>
            
            {{-- User Profile Mini --}}
            <div class="p-4 border-b border-gray-700 flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full flex items-center justify-center shadow-md">
                    <span class="font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-gray-400 text-sm truncate">{{ __('administrator') }}</p>
                </div>
            </div>
            
            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-6 hide-scrollbar">
                <div class="px-4 mb-6">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">{{ __('principal') }}</p>
                </div>
                
                @php
                    $navItems = [
                        ['route' => 'admin.dashboard', 'icon' => 'fas fa-chart-line', 'label' => __('dashboard')],
                        ['route' => 'admin.products.index', 'icon' => 'fas fa-box', 'label' => __('Produits')],
                        ['route' => 'admin.categories.index', 'icon' => 'fas fa-tags', 'label' => __('Catégories')],
                        ['route' => 'admin.orders.index', 'icon' => 'fas fa-shopping-cart', 'label' => __('Commandes')],
                        ['route' => 'admin.discounts.index', 'icon' => 'fas fa-percent', 'label' => __('reductions')],
                        ['route' => 'admin.banners.index', 'icon' => 'fas fa-layer-group', 'label' => __('banners')],
                    ];

                    $settingsItems = [
                        ['route' => 'admin.settings.index', 'icon' => 'fas fa-cog', 'label' => __('settings')],
                    ];
                @endphp
                
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="sidebar-link flex items-center px-4 py-3 mx-2 rounded-lg mb-1 hover:bg-gray-700/50 
                              {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'bg-gradient-to-r from-green-500/20 to-emerald-500/20 border-l-4 border-green-500 text-white' : 'text-gray-300' }}">
                        <i class="{{ $item['icon'] }} mr-3 w-5 text-center"></i>
                        <span class="font-medium">{{ $item['label'] }}</span>
                        @if(request()->routeIs(str_replace('.index', '.*', $item['route'])))
                            <div class="ml-auto w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        @endif
                    </a>
                @endforeach
                
                <div class="px-4 my-6">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">{{ __('system') }}</p>
                </div>
                
                @foreach($settingsItems as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="sidebar-link flex items-center px-4 py-3 mx-2 rounded-lg mb-1 hover:bg-gray-700/50 
                              {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'bg-gradient-to-r from-blue-500/20 to-cyan-500/20 border-l-4 border-blue-500 text-white' : 'text-gray-300' }}">
                        <i class="{{ $item['icon'] }} mr-3 w-5 text-center"></i>
                        <span class="font-medium">{{ $item['label'] }}</span>
                    </a>
                @endforeach
                
                <a href="{{ route('admin.profile.edit') }}" 
                   class="sidebar-link flex items-center px-4 py-3 mx-2 rounded-lg mb-1 hover:bg-gray-700/50 
                          {{ request()->routeIs('admin.profile.*') ? 'bg-gradient-to-r from-purple-500/20 to-pink-500/20 border-l-4 border-purple-500 text-white' : 'text-gray-300' }}">
                    <i class="fas fa-user-circle mr-3 w-5 text-center"></i>
                    <span class="font-medium">{{ __('my_profile') }}</span>
                </a>
            </nav>
            
            {{-- Footer Actions --}}
            <div class="border-t border-gray-700 p-4 space-y-2">
                <a href="{{ route('home') }}" target="_blank" 
                   class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-700/50 text-gray-300 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-external-link-alt mr-3"></i>
                        <span>{{ __('view_site') }}</span>
                    </div>
                    <i class="fas fa-external-link text-xs text-gray-400"></i>
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-red-500/10 text-red-400 hover:text-red-300 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            <span>{{ __('logout_fr') }}</span>
                        </div>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile Sidebar --}}
        <div class="sidebar-backdrop fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" id="sidebarBackdrop"></div>
        <aside class="sidebar-mobile fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white flex-col shadow-xl z-50 lg:hidden">
            {{-- Mobile Sidebar Header --}}
            <div class="p-6 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if($logoPath && file_exists(public_path('storage/'.$logoPath)))
                        <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}" 
                             class="h-10 w-auto object-contain">
                    @else 
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-2 rounded-lg">
                            <i class="fas fa-prescription-bottle-medical text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-lg font-bold">{{ __('admin_panel') }}</h1>
                        <p class="text-gray-400 text-xs">{{ __('pharmacy_management') }}</p>
                    </div>
                </div>
                <button id="closeMobileMenu" class="p-2 hover:bg-gray-700/50 rounded-lg">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            {{-- Mobile User Profile --}}
            <div class="p-4 border-b border-gray-700 flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full flex items-center justify-center shadow-md">
                    <span class="font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-gray-400 text-sm truncate">{{ __('administrator') }}</p>
                </div>
            </div>
            
            {{-- Mobile Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 hide-scrollbar">
                <div class="px-4 mb-4">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">{{ __('principal') }}</p>
                </div>
                
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="sidebar-link flex items-center px-4 py-3 mx-2 rounded-lg mb-1 hover:bg-gray-700/50 
                              {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'bg-gradient-to-r from-green-500/20 to-emerald-500/20 border-l-4 border-green-500 text-white' : 'text-gray-300' }}"
                       onclick="closeMobileMenu()">
                        <i class="{{ $item['icon'] }} mr-3 w-5 text-center"></i>
                        <span class="font-medium">{{ $item['label'] }}</span>
                    </a>
                @endforeach
                
                <div class="px-4 my-4">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">{{ __('system') }}</p>
                </div>
                
                @foreach($settingsItems as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="sidebar-link flex items-center px-4 py-3 mx-2 rounded-lg mb-1 hover:bg-gray-700/50 
                              {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'bg-gradient-to-r from-blue-500/20 to-cyan-500/20 border-l-4 border-blue-500 text-white' : 'text-gray-300' }}"
                       onclick="closeMobileMenu()">
                        <i class="{{ $item['icon'] }} mr-3 w-5 text-center"></i>
                        <span class="font-medium">{{ $item['label'] }}</span>
                    </a>
                @endforeach
                
                <a href="{{ route('admin.profile.edit') }}" 
                   class="sidebar-link flex items-center px-4 py-3 mx-2 rounded-lg mb-1 hover:bg-gray-700/50 
                          {{ request()->routeIs('admin.profile.*') ? 'bg-gradient-to-r from-purple-500/20 to-pink-500/20 border-l-4 border-purple-500 text-white' : 'text-gray-300' }}"
                   onclick="closeMobileMenu()">
                    <i class="fas fa-user-circle mr-3 w-5 text-center"></i>
                    <span class="font-medium">{{ __('my_profile') }}</span>
                </a>
            </nav>
            
            {{-- Mobile Footer Actions --}}
            <div class="border-t border-gray-700 p-4 space-y-2">
                <a href="{{ route('home') }}" target="_blank" 
                   class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-700/50 text-gray-300 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-external-link-alt mr-3"></i>
                        <span>{{ __('view_site') }}</span>
                    </div>
                    <i class="fas fa-external-link text-xs text-gray-400"></i>
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-red-500/10 text-red-400 hover:text-red-300 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            <span>{{ __('logout_fr') }}</span>
                        </div>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Desktop Header --}}
            <header class="hidden lg:flex bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-8 py-4 w-full">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-2xl font-bold text-gray-800">@yield('header', 'Tableau de bord')</h2>
                        @hasSection('subheader')
                            <span class="text-gray-400">/</span>
                            <span class="text-gray-600">@yield('subheader')</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        {{-- Notifications --}}
                        <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        {{-- Language Switcher --}}
                        <div class="hidden sm:flex rounded-full border border-gray-200 bg-white p-1 text-xs font-semibold">
                            <a href="{{ route('lang.switch', 'fr') }}"
                               class="px-3 py-1 rounded-full transition-colors {{ app()->getLocale() === 'fr' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:text-green-600' }}">
                                FR
                            </a>
                            <a href="{{ route('lang.switch', 'ar') }}"
                               class="px-3 py-1 rounded-full transition-colors {{ app()->getLocale() === 'ar' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:text-green-600' }}">
                                AR
                            </a>
                        </div>

                        {{-- User Menu --}}
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                <p class="text-sm text-gray-500">{{ __('administrator') }}</p>
                            </div>
                            <div class="relative">
                                <a href="{{ route('admin.profile.edit') }}" >
                                    <div class="user-avatar w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full flex items-center justify-center shadow-md cursor-pointer hover:shadow-lg">
                                        <span class="font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Messages --}}
            <div class="px-4 lg:px-8 pt-4 lg:pt-6">
                @if(session('success'))
                    <div class="mb-4 lg:mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm flex items-center animate-fade-in">
                        <div class="bg-green-500 p-2 rounded-lg mr-3 lg:mr-4">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-responsive-lg">Succès !</p>
                            <p class="text-responsive">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="ml-2 text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 lg:mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm flex items-center animate-fade-in">
                        <div class="bg-red-500 p-2 rounded-lg mr-3 lg:mr-4">
                            <i class="fas fa-exclamation-circle text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-responsive-lg">Erreur !</p>
                            <p class="text-responsive">{{ session('error') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="ml-2 text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 lg:mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm animate-fade-in">
                        <div class="flex items-center mb-2">
                            <div class="bg-red-500 p-2 rounded-lg mr-3 lg:mr-4">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                            <p class="font-semibold text-responsive-lg">Veuillez corriger les erreurs suivantes :</p>
                        </div>
                        <ul class="list-disc list-inside ml-8 lg:ml-10 space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-responsive">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-8 bg-gray-50">
                @yield('content')
            </main>
            
            {{-- Footer --}}
            <footer class="bg-white border-t border-gray-200 py-3 lg:py-4 px-4 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center justify-between text-sm text-gray-500 space-y-2 lg:space-y-0">
                    <div>
                        <span class="text-responsive">© {{ date('Y') }} {{ __('admin_panel') }}. {{ __('rights') }}</span>
                    </div>
                    <div class="flex flex-col lg:flex-row items-center lg:space-x-4 space-y-1 lg:space-y-0">
                        <span class="text-responsive">{{ __('last_login') }} {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'N/A' }}</span>
                        <span class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            <span class="text-responsive">{{ __('online') }}</span>
                        </span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <script>
        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const closeMobileMenuButton = document.getElementById('closeMobileMenu');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const mobileSidebar = document.querySelector('.sidebar-mobile');

        function openMobileMenu() {
            mobileSidebar.classList.add('active');
            sidebarBackdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            mobileSidebar.classList.remove('active');
            sidebarBackdrop.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', openMobileMenu);
        }

        if (closeMobileMenuButton) {
            closeMobileMenuButton.addEventListener('click', closeMobileMenu);
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', closeMobileMenu);
        }

        // Close mobile menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });

        // Close mobile menu when clicking on a link (handled in the HTML)
        document.querySelectorAll('.sidebar-mobile a').forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });

        // Add fade-in animation
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fade-in {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .animate-fade-in {
                    animation: fade-in 0.3s ease-out;
                }
                
                .sidebar-link.active {
                    background: linear-gradient(90deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
                    border-left: 4px solid #10b981;
                    color: white;
                }
                
                /* Responsive breakpoints for content */
                @media (max-width: 640px) {
                    .container-responsive {
                        padding-left: 1rem;
                        padding-right: 1rem;
                    }
                }
            `;
            document.head.appendChild(style);
            
            // Auto-hide success messages after 5 seconds
            setTimeout(() => {
                document.querySelectorAll('[class*="bg-gradient-to-r"]').forEach(alert => {
                    if (alert.textContent.includes('Succès') || alert.textContent.includes('Erreur')) {
                        alert.style.opacity = '0';
                        alert.style.transition = 'opacity 0.5s';
                        setTimeout(() => alert.remove(), 500);
                    }
                });
            }, 5000);
            
            // Close mobile menu on window resize (if resized to desktop)
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    closeMobileMenu();
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>